<template>
    <div class="conversation">
      <div class="header flex items-center justify-between p-4 border-b border-gray-300">
        <h1 class="text-lg font-semibold">{{ contact ? contact.name : 'Select a Contact' }}</h1>
        <div v-show="contact" class="flex items-center space-x-2">
          <button
            @click="callContact"
            class="call-button bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600 transition flex items-center space-x-2"
          >
            <span>📞</span>
            <span>Call</span>
          </button>
          <button
            v-show="aNewCallReceived"
            @click="answerCall"
            class="answer-button bg-blue-500 text-white px-4 py-2 rounded-full hover:bg-blue-600 transition"
          >
            Answer
          </button>
        </div>
      </div>
  
      <MessagesFeed :contact="contact" :messages="messages" />
      <MessageComposer @send="sendMessage" />
    </div>
  </template>
  
  <script>
  import MessagesFeed from './MessagesFeed.vue';
  import MessageComposer from './MessageComposer.vue';
  
  export default {
    props: {
      contact: {
        type: Object,
        default: null,
      },
      messages: {
        type: Array,
        default: () => [],
      },
      aNewCallReceived: {
        type: Boolean,
        default: false,
      },
    },
    methods: {
      sendMessage(text) {
        if (!this.contact) {
          return;
        }
        axios
          .post(
            'conversation/send',
            {
              contact_id: this.contact.id,
              text: text,
            },
            {
              timeout: 5000, // 5000 milisaniye (5 saniye) timeout süresi
            }
          )
          .then((response) => {
            this.$emit('new', response.data);
          });
      },
      callContact() {
        this.$emit('callContact', {
          receiverId: this.contact.id,
        });
      },
    },
    components: { MessagesFeed, MessageComposer },
  };
  </script>
  
  <style lang="scss" scoped>
  .conversation {
    flex: 5;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  
    .header {
      background-color: white;
      padding: 10px;
      border-bottom: 1px dashed lightgray;
  
      h1 {
        font-size: 20px;
        color: #333;
      }
  
      .call-button,
      .answer-button {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        line-height: 1;
        gap: 0.5rem;
      }
  
      .call-button {
        background-color: #22c55e;
        transition: background-color 0.2s ease;
      }
  
      .call-button:hover {
        background-color: #16a34a;
      }
  
      .answer-button {
        background-color: #3b82f6;
        transition: background-color 0.2s ease;
      }
  
      .answer-button:hover {
        background-color: #2563eb;
      }
    }
  }
  </style>
  