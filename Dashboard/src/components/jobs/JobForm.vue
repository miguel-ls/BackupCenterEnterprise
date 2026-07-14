<template>

<div class="bg-white rounded-xl">

    <div class="grid grid-cols-2 gap-6">

        <!-- Información general -->

        <div class="space-y-5">

            <div>

                <label class="block text-sm font-semibold mb-2">
                    Nombre del trabajo
                </label>

                <input
                    v-model="props.job.name"
                    placeholder="Ej.: Backup ERP Producción"
                    class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >

            </div>

            <div>

                <label class="block text-sm font-semibold mb-2">
                    Conexión
                </label>

                <select
                    v-model="props.job.connection_id"
                    class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >

                    <option :value="null">
                        Seleccione una conexión...
                    </option>

                    <option
                        v-for="connection in connections"
                        :key="connection.id"
                        :value="connection.id"
                    >
                        {{ connection.name }}
                    </option>

                </select>

            </div>

            <div>

                <label class="block text-sm font-semibold mb-2">
                    Programación
                </label>

                <input
                    v-model="props.job.schedule"
                    placeholder="0 */6 * * *"
                    class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >

                <p class="text-xs text-neutral-500 mt-2">
                    Expresión CRON.
                </p>

            </div>

        </div>

        <!-- Rutas -->

        <div class="space-y-5">

            <div>

                <label class="block text-sm font-semibold mb-2">
                    Carpeta origen
                </label>

                <input
                    v-model="props.job.source"
                    placeholder="D:\Backups"
                    class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >

            </div>

            <div>

                <label class="block text-sm font-semibold mb-2">
                    Carpeta destino
                </label>

                <input
                    v-model="props.job.destination"
                    placeholder="/backups/unimarket"
                    class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                >

            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">

                <div class="font-semibold text-blue-700 mb-2">
                    Información
                </div>

                <ul class="text-sm text-blue-700 space-y-1">

                    <li>• El origen corresponde a la carpeta local.</li>

                    <li>• El destino corresponde a la carpeta remota SFTP.</li>

                    <li>• El trabajo utilizará la conexión seleccionada.</li>

                </ul>

            </div>

        </div>

    </div>

    <div class="border-t mt-8 pt-6 flex justify-end gap-3">

        <button
            type="button"
            class="px-5 py-3 rounded-lg border hover:bg-neutral-100"
            @click="$emit('saved')"
        >
            Cancelar
        </button>

        <button
            @click="save"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold"
        >
            💾 Guardar trabajo
        </button>

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

    const response = await getConnections()

    connections.value = response.data

}

async function save(){

    if(!props.job.name){

        alert('Ingrese el nombre del trabajo.')

        return

    }

    if(!props.job.connection_id){

        alert('Seleccione una conexión.')

        return

    }

    if(props.job.id){

        await updateJob(props.job)

    }else{

        await createJob(props.job)

    }

    emit('saved')

}

onMounted(loadConnections)

</script>