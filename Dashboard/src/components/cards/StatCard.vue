<template>

<div class="card">

    <div class="title">
        {{ title }}
    </div>

    <div class="value">
        {{ value }}
    </div>

</div>

</template>

<script setup>

defineProps({

    title:String,

    value:[String,Number]

})

</script>

<style scoped>

.card{

    background:white;

    border-radius:12px;

    padding:25px;

    box-shadow:0 4px 12px rgba(0,0,0,.08);

    transition:.2s;

}

.card:hover{

    transform:translateY(-4px);

}

.title{

    color:#666;

    font-size:15px;

}

.value{

    margin-top:12px;

    font-size:32px;

    font-weight:bold;

}

</style>