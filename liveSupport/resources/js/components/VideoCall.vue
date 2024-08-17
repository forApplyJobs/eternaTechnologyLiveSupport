<template>
  <div v-show="connectionEstablished" class="video-call-container">
    <div id="videos">
      <div id="video-wrapper">
        <div v-if="waitingForAnswer" class="btn btn-warning">Waiting for answer...</div>
        <!-- Küçük video (Local Video) -->
        <video ref="localVideo" class="video-player small-video" autoplay playsinline muted controls></video>
        <!-- Görüşmeyi kapatma butonu -->
        <button @click="hangup" class="end-call-button">
          📞
        </button>
      </div>
      <!-- Büyük video (Remote Video) -->
      <video ref="remoteVideo" class="video-player large-video" autoplay playsinline controls></video>
      <!-- Ses çalma elementi (görünmez) -->
      <audio ref="callAudio" class="call-audio"></audio>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
function initialState (){
  return {
      userName: `Rob-${Math.floor(Math.random() * 100000)}`,
      sentCall:false,
      receiverId:null,
      localStream: null,
      connectionEstablished:false,
      remoteStream: null,
      aNewCallReceived:false,
      peerConnection: null,
      didIOffer: false,
      waitingForAnswer: false,
      peerConfiguration: {
        iceServers: [
          {
            urls: [
              'stun:stun.l.google.com:19302',
              'stun:stun1.l.google.com:19302',
            ],
          },
        ],
      },
    };
}
export default {
  props:{
    user:{
      type:Object,
      required:true
    },
  },
  data: function (){
    return initialState();
  }, 
  mounted() {
    this.initializeListeners();
  },
  methods: {
    initializeListeners() {
      Echo.private(`offer.${this.user.id}`)
        .listen('.newOffer', (e) => {
          if (!this.didIOffer) {
            this.answerOffer(e.offer);
          }
        });

      Echo.private(`answer.${this.user.id}`)
        .listen('.newAnswer', (e) => {
          this.addAnswer(e.answer);
        });

      Echo.private(`iceCandidates.${this.user.id}`)
        .listen('.newIceCandidate', (e) => {
          this.addNewIceCandidate(e.iceCandidate);
        });
      Echo.private(`calls.${this.user.id}`).listen('.call',(e)=>{
        if(e.call.status=='call_sent'){
          this.receiverId=e.call['call_from'];
          this.aNewCallReceived=true;
          this.$emit('aNewCallReceived',{
          call_from: this.user.id,
          receiver_id: this.receiverId,
          status: 'call_answered' 
          })

        }else if(e.call['status']=='call_answered'){
          this.receiverId=e.call['call_from'];
          this.call();
        }
      })
    },
    async callRequest($status){
      await axios.post('/send-call-request', {
          call_from: this.user.id,
          receiver_id: this.receiverId,
          status: $status 
        });
        
    },
    async call() {
      await this.fetchUserMedia();
      await this.createPeerConnection();
      try {
        const offer = await this.peerConnection.createOffer();
        await this.peerConnection.setLocalDescription(offer);
        this.didIOffer = true;
        await axios.post('/send-offer', {
          offer: offer,
          receiver_id: this.receiverId 
        });
        this.waitingForAnswer = true;
      } catch (err) {
        console.log(err);
      }
    },
    async answerOffer(offerObj) {
      try {
        await this.fetchUserMedia();
        offerObj.offer.sdp+="\r\n";
        await this.createPeerConnection(offerObj);
        await this.peerConnection.setRemoteDescription(offerObj.offer);
        const answer = await this.peerConnection.createAnswer();
        await this.peerConnection.setLocalDescription(answer);
        await axios.post('/send-answer', {
          answer: {
            ...answer,
            senderId: this.user.id 
          },
          receiver_id: this.receiverId  
        });
      } catch (error) {
        console.error("Error in answerOffer: ", error);
      }
    },
    async addAnswer(offerObj) {
      offerObj.answer.sdp+="\r\n"
      await this.peerConnection.setRemoteDescription(offerObj.answer);
      this.waitingForAnswer = false;
    },
    async fetchUserMedia() {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({
          video: true,
          audio:true
        });
        this.$refs.localVideo.srcObject = stream;
        this.localStream = stream;
      } catch (err) {
        console.log(err);
      }
    },
    async createPeerConnection(offerObj) {
      this.peerConnection = new RTCPeerConnection(this.peerConfiguration);
      this.remoteStream = new MediaStream();
      this.$refs.remoteVideo.srcObject = this.remoteStream;

      this.localStream.getTracks().forEach((track) => {
        this.peerConnection.addTrack(track, this.localStream);
      });

      this.peerConnection.addEventListener('icecandidate', (e) => {
        if (e.candidate) {
          axios.post('/send-ice-candidate', {
            iceCandidate: e.candidate,
            receiver_id: this.receiverId 
          });
        }
      });

      this.peerConnection.addEventListener('track', (e) => {
        e.streams[0].getTracks().forEach((track) => {
          this.remoteStream.addTrack(track);
          console.log(track)
        });
      });
      this.peerConnection.addEventListener('iceconnectionstatechange', () => {
        console.log('ICE Connection State:', this.peerConnection.iceConnectionState);
      });
      this.peerConnection.addEventListener('onnegotiationneeded', () => {
        console.log(this.peerConnection)
      });
      this.peerConnection.addEventListener('connectionstatechange', () => {
        console.log('Connection State:', this.peerConnection.connectionState);
        if(this.peerConnection.connectionState=='connected'){
          this.connectionEstablished=true;
          this.$emit('peersConnected')
        }else if(this.peerConnection.connectionState=='disconnected'){
          this.stopBothVideoAndAudio(this.$refs.localVideo.srcObject)
          this.resetWindow();
          this.$emit('video_call_ended')

        }

      });
      if (offerObj) {
        await this.peerConnection.setRemoteDescription(offerObj.offer);
      }
    },
    addNewIceCandidate(iceCandidate) {
      if(this.peerConnection.connectionState!=closed){
        this.peerConnection.addIceCandidate(iceCandidate.iceCandidate);
      }
    },
    handleIncomingMessage(message) {
      console.log("Incoming message: ", message);
    },
    answerCall(){
      this.callRequest('call_answered');
    },
    async hangup(){
      this.peerConnection.close();
      this.peerConnection=null;
      this.stopBothVideoAndAudio(this.$refs.localVideo.srcObject)
      this.resetWindow();
      this.$emit('video_call_ended')
    },
    stopBothVideoAndAudio(stream) {
    stream.getTracks().forEach((track) => {
        if (track.readyState == 'live') {
            track.stop();
        }
      });
    },
    resetWindow(){
        Object.assign(this.$data, initialState());
    }
  },
  

};
</script>

<style scoped>
.video-call-container {
  position: relative;
  width: 100%;
  height: 100%;
  background-color: #333;
  display: flex;
  justify-content: center;
  align-items: center;
}

#videos {
  position: relative;
  width: 80%;
  max-width: 1200px;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  background-color: black;
}

.video-player {
  width: 100%;
  height: auto;
  border-radius: 4px;
  background-color: black;
}

.large-video {
  width: 100%;
  height: auto;
  border-radius: 8px;
  pointer-events: none;
}

.small-video {
  position: absolute;
  width: 25%;
  bottom: 20px;
  right: 20px;
  border: 2px solid white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
  border-radius: 8px;
  background-color: black;
}

/* Görüşmeyi kapatma butonu */
.end-call-button {
  position: absolute;
  bottom: 25px;
  right: 30%;
  width: 45px;
  height: 45px;
  background-color: red;
  color: white;
  border: none;
  border-radius: 50%;
  font-size: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

.end-call-button:hover {
  background-color: darkred;
}

.call-audio {
  display: none;
}
</style>