<template>

    <div class="doc-card" @click="$emit('click')">

        <div class="icon">
            <slot name="icon"/>
        </div>

        <h2>
            <slot name="title"/>
        </h2>

        <p>
            <slot/>
        </p>

    </div>

</template>

<script setup>

defineEmits(["click"])

</script>

<style scoped>

.doc-card{

    background:white;

    border-radius:16px;

    padding:30px;

    cursor:pointer;

    transition:.25s;

    box-shadow:0 4px 15px rgba(0,0,0,.06);

    border:1px solid #ececec;

}

.doc-card:hover{

    transform:translateY(-6px);

    box-shadow:0 14px 30px rgba(0,0,0,.12);

}

.icon{

    font-size:42px;

    margin-bottom:20px;

}

h2{

    margin-bottom:15px;

}

p{

    color:#666;

    line-height:1.6;

}

</style>