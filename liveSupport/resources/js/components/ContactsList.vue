<template>
    <div class="contacts-list h-[32rem] overflow-y-scroll border-l border-gray-300">
      <ul class="list-none p-0 m-0">
        <li
          v-for="(contact, index) in contacts"
          :key="contact.id"
          @click="selectContact(index, contact)"
          :class="[
            'flex items-center p-2 cursor-pointer border-b border-gray-300 hover:bg-gray-100',
            index === selected ? 'bg-gray-200' : ''
          ]"
        >
          <div class="avatar flex-shrink-0">
            <img
              v-if="contact.avatar"
              :src="contact.avatar"
              alt="contact.name"
              class="w-10 h-10 rounded-full object-cover"
            />
          </div>
          <div class="contact flex-1 ml-3 text-sm">
            <p class="name font-semibold">{{ contact.name }}</p>
            <p class="email text-gray-500">{{ contact.email }}</p>
          </div>
          <span
            v-if="contact.unread"
            class="unread bg-green-400 text-white text-xs font-bold px-2 py-0.5 rounded-full"
          >
            {{ contact.unread }}
          </span>
        </li>
      </ul>
    </div>
  </template>

<script>
export default{
    props:{
        contacts:{
            type:Array,
            default:[]
        }
    },
    data(){
        return{
            selected:0
        }
    },
    methods:{
        selectContact(index,contact){
            console.log(contact)
            this.selected=index;
            this.$emit('selected',contact)
        }
    }
}
</script>
<style scoped>
.contacts-list {
  flex: 2;
  max-height: 100%;
}
</style>
