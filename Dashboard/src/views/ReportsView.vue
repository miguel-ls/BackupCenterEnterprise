<template>

<MainLayout>

    <div class="flex justify-between items-center mb-8">

        <div>

            <h1 class="text-3xl font-bold">

                Reportes

            </h1>

            <p class="text-neutral-500 mt-2">

                Dashboard Ejecutivo

            </p>

        </div>

        <div class="flex gap-3">

            <button
                @click="load"
                class="bg-gray-200 hover:bg-gray-300 px-5 py-3 rounded-lg"
            >
                Actualizar
            </button>

        </div>

    </div>

    <!-- KPIs -->

    <div class="grid grid-cols-5 gap-6">

        <StatCard
            title="Trabajos"
            :value="summary.jobs"
        />

        <StatCard
            title="Conexiones"
            :value="summary.connections"
        />

        <StatCard
            title="Total Archivos"
            :value="summary.uploaded_files"
        />

        <StatCard
            title="Ejecuciones"
            :value="summary.executions"
        />

        <StatCard
            title="Errores"
            :value="summary.errors"
        />

    </div>

    <!-- Clientes -->

    <div class="mt-8 bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b bg-neutral-50">

            <h2 class="text-lg font-bold">

                Ranking de Clientes

            </h2>

            <span class="text-sm text-neutral-500">

                {{ clients.length }} clientes

            </span>

        </div>

        <table class="w-full">

            <thead class="bg-neutral-100">

                <tr>

                    <th class="text-left p-3">Cliente</th>

                    <th class="text-right p-3">Ejecuciones</th>

                    <th class="text-right p-3">Archivos</th>

                    <th class="text-right p-3">Errores</th>

                </tr>

            </thead>

            <tbody>

                <tr
                    v-for="item in clients"
                    :key="item.client"
                    class="border-t hover:bg-blue-50 transition"
                >

                    <td class="p-3 font-medium">

                        {{ item.client }}

                    </td>

                    <td class="p-3 text-right">

                        {{ item.executions }}

                    </td>

                    <td class="p-3 text-right">

                        {{ item.uploaded }}

                    </td>

                    <td
                        class="p-3 text-right font-semibold"
                        :class="item.errors>0 ? 'text-red-600' : 'text-green-600'"
                    >

                        {{ item.errors }}

                    </td>

                </tr>

                <tr
                    v-if="clients.length===0"
                >

                    <td
                        colspan="4"
                        class="text-center py-10 text-neutral-500"
                    >

                        No existen registros.

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

    <div class="mt-5 text-right text-sm text-neutral-500">

        Última actualización:
        <strong>{{ lastUpdate }}</strong>

    </div>

</MainLayout>

</template>

<script setup>

import {

    ref

} from "vue"

import MainLayout from "../components/layout/MainLayout.vue"

import StatCard from "../components/cards/StatCard.vue"

import {

    getReportSummary,
    getReportClients

} from "../api/client"

import {

    useAutoRefresh

} from "../composables/useAutoRefresh"

const summary = ref({

    jobs:0,

    connections:0,

    uploaded_files:0,

    executions:0,

    errors:0

})

const clients = ref([])

const lastUpdate = ref("-")

async function load(){

    try{

        const [

            summaryResponse,

            clientsResponse

        ] = await Promise.all([

            getReportSummary(),

            getReportClients()

        ])

        summary.value = summaryResponse.data

        clients.value = clientsResponse.data

        lastUpdate.value = new Date().toLocaleTimeString()

    }
    catch(e){

        console.error(e)

    }

}

useAutoRefresh(load,5000)

</script>