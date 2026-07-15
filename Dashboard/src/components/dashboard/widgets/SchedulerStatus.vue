<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm">

    <div class="flex items-center justify-between px-6 py-5 border-b">

        <div>

            <h2 class="text-xl font-bold">

                Centro de Monitoreo

            </h2>

            <div class="text-sm text-neutral-500">

                Actualización automática cada 5 segundos

            </div>

        </div>

        <div class="flex items-center gap-2">

            <span
                class="w-3 h-3 rounded-full"
                :class="online ? 'bg-green-500 animate-pulse' : 'bg-red-500'"
            ></span>

            <span
                class="font-semibold"
                :class="online ? 'text-green-600' : 'text-red-600'"
            >

                {{ online ? "ONLINE" : "OFFLINE" }}

            </span>

        </div>

    </div>

    <div class="grid grid-cols-2 gap-4 p-6">

        <div class="card">

            <div class="label">

                Scheduler

            </div>

            <div
                class="value"
                :class="badge(data.scheduler)"
            >

                {{ data.scheduler }}

            </div>

        </div>

        <div class="card">

            <div class="label">

                Worker

            </div>

            <div
                class="value"
                :class="badge(data.worker)"
            >

                {{ data.worker }}

            </div>

        </div>

        <div class="card">

            <div class="label">

                Pendientes

            </div>

            <div class="number">

                {{ data.queue }}

            </div>

        </div>

        <div class="card">

            <div class="label">

                Ejecutándose

            </div>

            <div class="number text-blue-600">

                {{ data.running }}

            </div>

        </div>

        <div class="card">

            <div class="label">

                Completados

            </div>

            <div class="number text-green-600">

                {{ data.completed }}

            </div>

        </div>

        <div class="card">

            <div class="label">

                Fallidos

            </div>

            <div
                class="number"
                :class="data.failed>0 ? 'text-red-600' : 'text-green-600'"
            >

                {{ data.failed }}

            </div>

        </div>

    </div>

    <div class="border-t p-6">

        <div class="flex justify-between mb-3">

            <span class="text-neutral-500">

                Total ejecuciones

            </span>

            <strong>

                {{ data.executions }}

            </strong>

        </div>

        <div class="flex justify-between">

            <span class="text-neutral-500">

                Última ejecución

            </span>

            <strong>

                {{ data.last_execution || "-" }}

            </strong>

        </div>

    </div>

</div>

</template>

<script setup>

import {

    ref,
    onMounted,
    onUnmounted,
    computed

} from "vue"

import {

    getSystemStatus

} from "@/api/client"

const data = ref({

    scheduler:"-",

    worker:"-",

    queue:0,

    running:0,

    completed:0,

    failed:0,

    executions:0,

    last_execution:"-"

})

const online = computed(()=>{

    return data.value.scheduler==="Activo"

})

function badge(value){

    return value==="Activo"

        ? "text-green-600"

        : "text-red-600"

}

async function load(){

    try{

        const response = await getSystemStatus()

        data.value = response.data

    }

    catch(error){

        console.error(error)

    }

}

let timer = null

onMounted(()=>{

    load()

    timer = setInterval(load,5000)

})

onUnmounted(()=>{

    clearInterval(timer)

})

</script>

<style scoped>

.card{

    border:1px solid #e5e7eb;

    border-radius:12px;

    padding:18px;

    transition:.2s;

}

.card:hover{

    box-shadow:0 10px 25px rgba(0,0,0,.08);

    transform:translateY(-2px);

}

.label{

    font-size:13px;

    color:#6b7280;

    margin-bottom:10px;

}

.number{

    font-size:30px;

    font-weight:700;

}

.value{

    font-size:20px;

    font-weight:700;

}

</style>