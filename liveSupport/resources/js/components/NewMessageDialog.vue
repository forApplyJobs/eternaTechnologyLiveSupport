<template>
    <div class="fixed bottom-4 right-4 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-lg p-4 w-72 max-w-full space-y-3">
        <h1 class="text-lg font-semibold text-gray-800 text-center">You have a new message</h1>
        <h1 class="text-md font-semibold text-gray-700 text-center">{{ messageFrom }}</h1>
        <h2 class="text-sm text-gray-600 text-center">{{ message }}</h2>
      </div>
    </div>
  </template>
<script>
export default{
    props:{
        messageFrom:null,
        message:null
    }
}
</script>