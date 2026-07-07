<template>

<MainLayout>

    <h1 class="text-3xl font-bold mb-8">

        Configuración

    </h1>

    <div class="bg-white rounded-xl shadow-sm border border-neutral-200 p-8">

        <div class="grid grid-cols-2 gap-6">

            <div>

                <label class="block mb-2 font-medium">

                    Intervalo Scheduler (segundos)

                </label>

                <input
                    v-model.number="settings.scheduler_interval"
                    type="number"
                    class="w-full border rounded-lg p-3"
                >

            </div>

            <div>

                <label class="block mb-2 font-medium">

                    Máximo de Hilos

                </label>

                <input
                    v-model.number="settings.max_threads"
                    type="number"
                    class="w-full border rounded-lg p-3"
                >

            </div>

            <div>

                <label class="block mb-2 font-medium">

                    Reintentos

                </label>

                <input
                    v-model.number="settings.retry_count"
                    type="number"
                    class="w-full border rounded-lg p-3"
                >

            </div>

            <div>

                <label class="block mb-2 font-medium">

                    Retención (días)

                </label>

                <input
                    v-model.number="settings.retention_days"
                    type="number"
                    class="w-full border rounded-lg p-3"
                >

            </div>

            <div>

                <label class="block mb-2 font-medium">

                    Timeout Conexión

                </label>

                <input
                    v-model.number="settings.connection_timeout"
                    type="number"
                    class="w-full border rounded-lg p-3"
                >

            </div>

            <div>

                <label class="block mb-2 font-medium">

                    Nivel de Log

                </label>

                <select
                    v-model="settings.log_level"
                    class="w-full border rounded-lg p-3"
                >

                    <option>DEBUG</option>
                    <option>INFO</option>
                    <option>WARNING</option>
                    <option>ERROR</option>

                </select>

            </div>

            <div class="col-span-2">

                <label class="block mb-2 font-medium">

                    Carpeta de Logs

                </label>

                <input
                    v-model="settings.log_path"
                    class="w-full border rounded-lg p-3"
                >

            </div>

            <div>

                <label class="flex items-center gap-3">

                    <input
                        type="checkbox"
                        v-model="compressionEnabled"
                    >

                    Compresión habilitada

                </label>

            </div>

        </div>

        <div class="mt-8 flex justify-end">

            <button
                @click="save"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg"
            >

                Guardar Configuración

            </button>

        </div>

    </div>

</MainLayout>

</template>

<script setup>

import { reactive, onMounted, computed } from "vue";

import MainLayout from "../components/layout/MainLayout.vue";

import {

    getSettings,
    updateSettings

} from "../api/client";

const settings = reactive({

    scheduler_interval:60,
    max_threads:4,
    retry_count:3,
    retention_days:30,
    compression:1,
    log_level:"INFO",
    log_path:"",
    connection_timeout:30

});

const compressionEnabled = computed({

    get(){

        return settings.compression===1;

    },

    set(value){

        settings.compression=value?1:0;

    }

});

async function load(){

    const response=await getSettings();

    Object.assign(settings,response.data);

}

async function save(){

    await updateSettings(settings);

    alert("Configuración guardada correctamente.");

}

onMounted(load);

</script>