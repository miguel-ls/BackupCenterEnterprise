<template>

<div class="bg-white rounded-xl">

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

        <div class="flex justify-end">

            <button
                @click="save"
                class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700"
            >
                Guardar
            </button>

        </div>

    </div>

</div>

</template>

<script setup>

import { reactive } from 'vue'
import { createJob } from '../../api/client'

const emit = defineEmits(['saved'])

const job = reactive({

    name:'',
    source:'',
    destination:'',
    schedule:''

})

async function save(){

    await createJob(job)

    emit('saved')

}

</script>