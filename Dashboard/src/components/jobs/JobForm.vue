<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">

    <h2 class="text-xl font-bold mb-6">
        Nuevo Trabajo
    </h2>

    <div class="space-y-4">

        <input
            v-model="job.name"
            placeholder="Nombre del trabajo"
            class="w-full border rounded-lg p-3"
        >

        <input
            v-model="job.source"
            placeholder="Origen"
            class="w-full border rounded-lg p-3"
        >

        <input
            v-model="job.destination"
            placeholder="Destino"
            class="w-full border rounded-lg p-3"
        >

        <input
            v-model="job.schedule"
            placeholder="Cron (0 */6 * * *)"
            class="w-full border rounded-lg p-3"
        >

        <button
            class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700"
        >
            Guardar
        </button>

    </div>

</div>

</template>

<script setup>

import { reactive } from 'vue'

const job = reactive({

    name:'',
    source:'',
    destination:'',
    schedule:''

})

</script>