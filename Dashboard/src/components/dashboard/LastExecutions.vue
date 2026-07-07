<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm">

    <div class="flex items-center justify-between p-5 border-b">

        <h2 class="text-lg font-semibold">

            Últimas ejecuciones

        </h2>

        <button
            @click="load"
            class="text-sm text-blue-600 hover:underline"
        >
            Actualizar
        </button>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead class="bg-neutral-50">

                <tr>

                    <th class="text-left p-3 font-semibold">Fecha</th>
                    <th class="text-left p-3 font-semibold">Cliente</th>
                    <th class="text-center p-3 font-semibold">Subidos</th>
                    <th class="text-center p-3 font-semibold">Errores</th>
                    <th class="text-center p-3 font-semibold">Tiempo</th>
                    <th class="text-center p-3 font-semibold">Estado</th>

                </tr>

            </thead>

            <tbody>

                <tr
                    v-for="item in history"
                    :key="item.id"
                    class="border-t hover:bg-neutral-50 transition"
                >

                    <td class="p-3 whitespace-nowrap">

                        {{ formatDate(item.executed_at) }}

                    </td>

                    <td class="p-3 font-medium">

                        {{ item.client }}

                    </td>

                    <td class="p-3 text-center">

                        {{ item.files_uploaded }}

                    </td>

                    <td class="p-3 text-center">

                        {{ item.errors }}

                    </td>

                    <td class="p-3 text-center">

                        {{ Number(item.duration).toFixed(2) }} s

                    </td>

                    <td class="p-3 text-center">

                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold"
                            :class="badgeClass(item.status)"
                        >
                            {{ item.status }}
                        </span>

                    </td>

                </tr>

                <tr v-if="history.length === 0">

                    <td
                        colspan="6"
                        class="text-center p-10 text-neutral-500"
                    >

                        No existen ejecuciones.

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

</template>

<script setup>

import {
    ref,
    onMounted
} from "vue";

import {
    getHistory
} from "@/api/client";

const history = ref([]);

async function load(){

    try{

        const response = await getHistory();

        history.value = response.data ?? [];

    }catch(error){

        console.error(error);

        history.value = [];

    }

}

function formatDate(date){

    if(!date){

        return "-";

    }

    return new Date(date.replace(" ","T")).toLocaleString();

}

function badgeClass(status){

    switch(status){

        case "OK":

            return "bg-green-100 text-green-700";

        case "ERROR":

            return "bg-red-100 text-red-700";

        case "WARNING":

            return "bg-yellow-100 text-yellow-700";

        default:

            return "bg-gray-100 text-gray-700";

    }

}

onMounted(load);

</script>