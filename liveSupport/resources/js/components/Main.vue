

<template>
  <div class="chat-app">
    <Conversation :contact="selectedContact" :messages="messages" @new="saveNewMessage"/>
    <ContactsList :contacts="contacts" @selected="startConversationWith" />
  </div>
</template>

<script>
import Conversation from './Conversation.vue';
import ContactsList from './ContactsList.vue';
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
        contacts:[]
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
      }
    },
    components:{Conversation,ContactsList}

      
    }
  
</script>

<style lang="scss" scoped>
.chat-app{
  display: flex;
}
</style>