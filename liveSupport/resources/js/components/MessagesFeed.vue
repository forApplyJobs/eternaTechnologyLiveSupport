<template>
    <div class="feed bg-gray-100 h-[470px] overflow-y-scroll p-4 rounded-lg shadow-md border border-gray-300" ref="feed">
      <ul v-if="contact" class="space-y-4">
        <li
          v-for="message in messages"
          :key="message.id"
          :class="[
            'flex items-start space-x-4',
            message.messageTo === contact.id ? 'justify-end' : 'justify-start'
          ]"
        >
          <div
            class="text max-w-xs px-4 py-2 rounded-lg"
            :class="{
              'bg-gray-300 text-gray-800': message.messageTo === contact.id,
              'bg-blue-500 text-white': message.messageTo !== contact.id
            }"
          >
            {{ message.text }}
          </div>
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      contact: {
        type: Object,
      },
      messages: {
        type: Array,
        required: true,
      },
    },
    methods: {
      scrollToBottom() {
        this.$nextTick(() => {
          const feed = this.$refs.feed;
          feed.scrollTop = feed.scrollHeight - feed.clientHeight;
        });
      },
    },
    watch: {
      messages: {
        handler() {
          this.scrollToBottom();
        },
        deep: true,
      },
      contact() {
        this.scrollToBottom();
      },
    },
  };
  </script>
  
  <style lang="scss" scoped>
  .feed {
    height: 470px; 
    overflow-y: scroll;
    scrollbar-width: thin; 
    scrollbar-color: #94a3b8 #e5e7eb; 
  
    &::-webkit-scrollbar {
      width: 8px;
    }
  
    &::-webkit-scrollbar-track {
      background: #e5e7eb; 
      border-radius: 10px;
    }
  
    &::-webkit-scrollbar-thumb {
      background-color: #94a3b8; 
      border-radius: 10px;
      border: 2px solid #e5e7eb;
    }
  
    ul {
      list-style-type: none;
      padding: 5px;
    }
  
    li {
      &.message {
        margin: 10px 0;
        width: 100%;
  
        .text {
          max-width: 200px;
          border-radius: 5px;
          padding: 12px;
          display: inline-block;
        }
  
        &.received {
          text-align: right;
  
          .text {
            background: #b2b2b2;
          }
        }
  
        &.sent {
          text-align: left;
  
          .text {
            background: #81c4f9;
          }
        }
      }
    }
  }
  </style>
  