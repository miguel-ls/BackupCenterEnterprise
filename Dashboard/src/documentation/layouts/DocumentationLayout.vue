<template>

    <MainLayout>

        <div class="documentation-layout">

            <slot />

        </div>

    </MainLayout>

</template>

<script setup>

import MainLayout from "../../components/layout/MainLayout.vue"

</script>

<style scoped>

.documentation-layout{

    max-width: 1400px;
    margin: 0 auto;

}

</style>