<template>
    <div class="conversation">
        <button v-show="contact" @click="callContact">Call</button>
        <button v-show="aNewCallReceived" @click="answerCall"></button>
        <h1>{{ contact? contact.name :'Select a Contact' }}</h1>
        <MessagesFeed :contact="contact" :messages="messages"></MessagesFeed>
        <MessageComposer @send="sendMessage"/>
    </div>
</template>

<script>
    import MessagesFeed from './MessagesFeed.vue'
    import MessageComposer from './MessageComposer.vue'
export default{
    
    props:{
        contact:{
            type:Object,
            default:null
        },
        messages:{
            type:Array,
            default:[]
        },
        aNewCallReceived:{
            type:Boolean,
            default:false
        }
    },
    methods:{
        sendMessage(text){
            if(!this.contact){
                return;
            }
            console.log("contactid="+this.contact.id)
            axios.post('conversation/send', {
                contact_id: this.contact.id,
                text: text
            }, {
                timeout: 5000 // 5000 milisaniye (5 saniye) timeout süresi
            }).then((response) => {
                this.$emit('new', response.data);
            });
        },
        callContact(){
            this.$emit('callContact',{
                receiverId:this.contact.id
            })
        }
    },
    components:{MessagesFeed,MessageComposer}
}
</script>

<style lang="scss" scoped>
.conversation {
    flex: 5;
    display: flex;
    flex-direction: column;
    justify-content: space-between;

    h1 {
        font-size: 20px;
        padding: 10px;
        margin: 0;
        border-bottom: 1px dashed lightgray;
    }
}
</style>