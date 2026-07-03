<template>

<div class="bg-white rounded-xl">

    <div class="space-y-4">

        <input
            v-model="props.job.name"
            placeholder="Nombre del trabajo"
            class="w-full border rounded-lg p-3"
        >

        <input
            v-model="props.job.source"
            placeholder="Origen"
            class="w-full border rounded-lg p-3"
        >

        <input
            v-model="props.job.destination"
            placeholder="Destino"
            class="w-full border rounded-lg p-3"
        >

        <input
            v-model="props.job.schedule"
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


import { createJob, updateJob } from '../../api/client'

const emit = defineEmits(['saved'])

const props = defineProps({

    job: Object

})

async function save(){

    console.log(props.job)

    console.log("ID:", props.job.id)

    if(props.job.id){

        console.log("EDITAR")

        await updateJob(props.job)

    }else{

        console.log("CREAR")

        await createJob(props.job)

    }

    emit('saved')

}

</script>