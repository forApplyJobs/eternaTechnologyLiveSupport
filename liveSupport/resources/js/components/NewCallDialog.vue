<template>
<div>
    <h1>You have a call from {{ caller }}</h1>
    <button>Accept</button>
</div>

</template>
<script>
    export default{
        props:{
            caller:null
        }
    }
</script>