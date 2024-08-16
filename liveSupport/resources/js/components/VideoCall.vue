<template>
  <div class="container">
    <div class="row mb-3 mt-3 justify-content-md-center">
      <div>{{ userName }}</div>
      <input v-model="receiverId" type="number" placeholder="Enter Receiver ID" class="form-control col-2 mb-2" />
      <button @click="callRequest('call_sent')" class="btn btn-primary col-1">Call!</button>
      <button @click="hangup" class="col-1 btn btn-primary">Hangup</button>
      <div id="answer" class="col"></div>
      <button v-if="this.aNewCallReceived" @click="answerCall" class="col-1 btn btn-primary">Answer call from{{ this.receiverId }}</button>
    </div>
    <div id="videos">
      <div id="video-wrapper">
        <div v-if="waitingForAnswer" class="btn btn-warning">Waiting for answer...</div>
        <video ref="localVideo" class="video-player" autoplay playsinline muted controls></video>
      </div>
      <video ref="remoteVideo" class="video-player" autoplay playsinline muted controls></video>
      <audio ref="callAudio" class="call-audio" controls></audio>
    </div>
  </div>
</template>


<script>
import axios from 'axios';

export default {
  props:{
    user:{
      type:Object,
      required:true
    }
  },
  data() {
    return {
      userName: `Rob-${Math.floor(Math.random() * 100000)}`,
      sentCall:false,
      localStream: null,
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
        }else if(e.call['status']=='call_answered'){
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
      });
      if (offerObj) {
        await this.peerConnection.setRemoteDescription(offerObj.offer);
      }
    },
    addNewIceCandidate(iceCandidate) {
      this.peerConnection.addIceCandidate(iceCandidate.iceCandidate);
    },
    handleIncomingMessage(message) {
      console.log("Incoming message: ", message);
    },
    answerCall(){
      this.callRequest('call_answered');
    }
  },
};
</script>

<style scoped>
.video-player {
  width: 100%;
  height: auto;
  background-color: black;
}
</style>
