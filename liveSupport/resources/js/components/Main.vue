

<template>
  <div class="chat-app" v-show="isChatActive">
    <Conversation @callContact="callContactReq" :contact="selectedContact" :messages="messages" @new="saveNewMessage"/>
    <ContactsList :contacts="contacts" @selected="startConversationWith" />
    
  </div>
  <VideoCall @aNewCallReceived="showCallDialog" @peersConnected="changeVisibility" @video_call_ended="changeVisibility" :user="this.user"></VideoCall>
  <NewCallDialog v-if="callReceived" :caller="callObj.call_from" @click="handleReceivedCall"></NewCallDialog>
</template>

<script>
import Conversation from './Conversation.vue';
import ContactsList from './ContactsList.vue';
import VideoCall from './VideoCall.vue';
import NewCallDialog from './NewCallDialog.vue';
  export default {
    props:{
      user:{
        type:Object,
        reqired:true
      }
    },
    data(){
      return{
        selectedContact:null,
        messages:[],
        contacts:[],
        isChatActive:true,
        callReceived:false,
        callObj:null
      };
    },
    mounted() {
      console.log(this.user.id);
      Echo.private(`messages.${this.user.id}`)
                .listen('.message', (e) => {
                    console.log("it is working");
                    this.handleIncoming(e.message);
                });

        axios.get('/contacts').then((response)=>{
          console.log(response.data)
          this.contacts= response.data;
          
        });
    },
    methods:{
      startConversationWith(contact){
        axios.get(`conversation/${contact.id}`).then((response)=>{
          console.log(response.data)
          this.messages=response.data;
          this.selectedContact = contact;
        })
      },
      saveNewMessage(obj){
        this.messages.push(obj);
      },
      handleIncoming(message){
        if(this.selectedContact&& message.messageFrom==this.selectedContact.id){
          this.saveNewMessage(message);
        }
        alert(message.text);
      },
      async callContactReq(obj){
        await axios.post('/send-call-request', {
          call_from: this.user.id,
          receiver_id: obj.receiverId,
          status: 'call_sent' 
        });
      },
      changeVisibility(){
        console.log("working")
        this.isChatActive=!this.isChatActive;
      },
      showCallDialog(obj){
        this.callReceived=true;
        this.callObj=obj
      },
      async handleReceivedCall(){
        console.log(this.callObj);
        await axios.post('/send-call-request',this.callObj);
        this.callReceived=false;
      }
    },
    components:{Conversation,ContactsList,VideoCall,NewCallDialog}

      
    }
  
</script>

<style lang="scss" scoped>
.chat-app{
  display: flex;
}
</style>