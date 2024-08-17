

<template>
  <div class="chat-app" v-show="isChatActive">
    <Conversation @callContact="callContactReq" :contact="selectedContact" :messages="messages" @new="saveNewMessage"/>
    <ContactsList :contacts="contacts" @selected="startConversationWith" />
    
  </div>
  <VideoCall @aNewCallReceived="showCallDialog" @peersConnected="changeVisibility" @video_call_ended="changeVisibility" :user="this.user"></VideoCall>
  <NewCallDialog v-if="callReceived" :caller="callObj.call_from" @call_accepted="handleReceivedCall" @call_declined="hidCallDialog"></NewCallDialog>
  <NewMessageDialog @click="closeNewMessage" :messageFrom="messageObj.messageFrom" :message="messageObj.text" v-if="messageReceived"></NewMessageDialog>
</template>

<script>
import Conversation from './Conversation.vue';
import ContactsList from './ContactsList.vue';
import VideoCall from './VideoCall.vue';
import NewCallDialog from './NewCallDialog.vue';
import NewMessageDialog from './newMessageDialog.vue';
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
        callObj:null,
        messageReceived:false,
        messageObj:null
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
          console.log(this.contacts)
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
      handleIncoming(messageObj){
        if(this.selectedContact&& messageObj.messageFrom==this.selectedContact.id){
          this.saveNewMessage(messageObj);
        }else{
          console.log(this.contacts[messageObj.messageFrom])
          console.log(messageObj)
          this.messageObj=messageObj;
          this.messageReceived=true;
        }
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
      hidCallDialog(){
        console.log("hidsene oglimm")
        this.callReceived=false;
      },
      async handleReceivedCall(){
        console.log(this.callObj);
        await axios.post('/send-call-request',this.callObj);
        this.callReceived=false;
      },
      closeNewMessage(){
        this.messageReceived=false;
        this.messageObj=null;
        
      }
    },
    components:{Conversation,ContactsList,VideoCall,NewCallDialog,NewMessageDialog},
    
    
      
    }
  
</script>

<style lang="scss" scoped>
.chat-app{
  display: flex;
}
</style>