<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">

    <div class="flex items-center justify-between mb-6">

        <h2 class="text-xl font-bold">

            Estado del Sistema

        </h2>

        <div class="flex items-center gap-2 text-green-600 font-semibold">

            <span class="w-3 h-3 rounded-full bg-green-500"></span>

            Operativo

        </div>

    </div>

    <div class="space-y-4">

        <div class="flex justify-between items-center">

            <span>Scheduler</span>

            <span
                class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold"
            >
                {{ data.scheduler }}
            </span>

        </div>

        <div class="flex justify-between items-center">

            <span>Worker</span>

            <span
                class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold"
            >
                {{ data.worker }}
            </span>

        </div>

        <div class="flex justify-between items-center">

            <span>Pendientes</span>

            <span class="font-bold">

                {{ data.queue }}

            </span>

        </div>

        <div class="flex justify-between items-center">

            <span>Ejecutándose</span>

            <span class="font-bold">

                {{ data.running }}

            </span>

        </div>

        <div class="flex justify-between items-center">

            <span>Con errores</span>

            <span
                class="font-bold"
                :class="data.failed>0
                    ? 'text-red-600'
                    : 'text-green-600'"
            >

                {{ data.failed }}

            </span>

        </div>

        <hr>

        <div class="flex justify-between">

            <span>Total ejecuciones</span>

            <span class="font-bold">

                {{ data.executions }}

            </span>

        </div>

        <div>

            <div class="text-sm text-neutral-500 mb-1">

                Última ejecución

            </div>

            <div class="font-semibold">

                {{ data.last_execution || '-' }}

            </div>

        </div>

    </div>

</div>

</template>

<script setup>

import {
    ref,
    onMounted,
    onUnmounted
} from "vue";

import {
    getSystemStatus
} from "@/api/client";

const data = ref({});

let timer = null;

async function load(){

    const response = await getSystemStatus();

    data.value = response.data;

}

onMounted(()=>{

    load();

    timer = setInterval(load,5000);

});

onUnmounted(()=>{

    clearInterval(timer);

});

</script>