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
    placeholder="Carpeta remota (/backups/unimarket)"
    class="w-full border rounded-lg p-3"
>

        <input
            v-model="props.job.schedule"
            placeholder="Cron (0 */6 * * *)"
            class="w-full border rounded-lg p-3"
        >

        <!-- NUEVO -->

        <select
            v-model="props.job.connection_id"
            class="w-full border rounded-lg p-3"
        >

            <option :value="null">

                Seleccione una conexión

            </option>

            <option
                v-for="connection in connections"
                :key="connection.id"
                :value="connection.id"
            >

                {{ connection.name }}

            </option>

        </select>

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

import {
    onMounted,
    ref
} from 'vue'

import {
    createJob,
    updateJob,
    getConnections
} from '../../api/client'

const emit = defineEmits([
    'saved'
])

const props = defineProps({

    job:Object

})

const connections = ref([])

async function loadConnections(){

    connections.value = await getConnections()

}

async function save(){

    if(props.job.id){

        await updateJob(props.job)

    }else{

        await createJob(props.job)

    }

    emit('saved')

}

onMounted(loadConnections)

</script>