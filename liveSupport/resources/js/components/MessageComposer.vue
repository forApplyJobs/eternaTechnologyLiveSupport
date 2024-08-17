<template>
    <div class="composer p-4">
      <textarea
        v-model="message"
        @keydown.enter="send"
        placeholder="Message.."
        class="w-full h-24 p-3 border border-gray-300 rounded-lg resize-none focus:border-blue-500 focus:outline-none"
      ></textarea>
    </div>
  </template>
<script>
export default{
    data(){

        
    },
    methods:{
        send(e){
            e.preventDefault();
            
            if(this.message==''){
                return;
            }
            this.$emit('send',this.message)
            this.message=''
            e.currentTarget.value = "";
        }
    }

}
</script>

<style lang="scss" scoped>
.composer textarea {
    width: 96%;
    margin: 10px;
    resize: none;
    border-radius: 3px;
    border: 1px solid lightgray;
    padding: 6px;
}
</style>