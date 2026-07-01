<template>

<MainLayout>

    <h1 class="text-3xl font-bold mb-8">

        Dashboard

    </h1>

    <div class="grid grid-cols-4 gap-6">

        <StatCard
            title="Versión"
            :value="status.version"
        />

        <StatCard
            title="Servicio"
            :value="status.service"
        />

        <StatCard
            title="PHP"
            :value="status.php"
        />

        <StatCard
            title="Base de datos"
            :value="status.database ? 'OK' : 'ERROR'"
        />

    </div>

</MainLayout>

</template>

<script setup>

import { onMounted, ref } from 'vue'

import MainLayout from '../components/layout/MainLayout.vue'
import StatCard from '../components/cards/StatCard.vue'

import { getStatus } from '../api/client'

const status = ref({

    version:'...',

    service:'...',

    php:'...',

    database:false

})

onMounted(async()=>{

    status.value = await getStatus()

})

</script>