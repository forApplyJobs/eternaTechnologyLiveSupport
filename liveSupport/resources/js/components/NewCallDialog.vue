<template>
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="flex flex-col items-center justify-center w-64 bg-blue-50 rounded-lg shadow-lg p-4 space-y-3">
        <h1 class="text-base font-semibold text-gray-800 text-center">You have a call from {{ caller }}</h1>
        <button @click="callAccept" class="px-4 py-2 bg-green-500 text-white font-bold rounded-full hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
          Accept
        </button>
        <button @click="callDecline" class="px-4 py-2 bg-red-500 text-white font-bold rounded-full hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-green-300">
          Decline
        </button>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      caller: {
        default: null
      }
    },
    methods:{
        callAccept(){
            this.$emit('call_accepted')
        },
        callDecline(){
            this.$emit('call_declined')
        }
    }
  }
  </script>
  
  <style scoped>
  /* Dialog background styling for overlay */
  .bg-black {
    background-color: rgba(0, 0, 0, 0.5);
  }
  </style>
  