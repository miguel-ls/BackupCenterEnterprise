<template>

<MainLayout>

    <div class="flex justify-between items-center mb-8">

        <h1 class="text-3xl font-bold">

            Historial

        </h1>

        <div class="flex items-center gap-3">

            <select
                v-model="limit"
                @change="changeLimit"
                class="border rounded-lg px-3 py-2"
            >
                <option :value="10">10</option>
                <option :value="20">20</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
            </select>

            <button
                @click="loadHistory"
                class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700"
            >
                Actualizar
            </button>

        </div>

    </div>

    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">

        <table class="w-full">

            <thead class="bg-neutral-100">

                <tr>

                    <th class="text-left p-3">Fecha</th>
                    <th class="text-left p-3">Cliente</th>
                    <th class="text-left p-3">Trabajo</th>
                    <th class="text-left p-3">Encontrados</th>
                    <th class="text-left p-3">Subidos</th>
                    <th class="text-left p-3">Omitidos</th>
                    <th class="text-left p-3">Errores</th>
                    <th class="text-left p-3">Duración</th>
                    <th class="text-left p-3">Estado</th>

                </tr>

            </thead>

            <tbody>

                <tr
                    v-for="item in history"
                    :key="item.id"
                    class="border-t hover:bg-neutral-50"
                >

                    <td class="p-3">{{ item.executed_at }}</td>
                    <td class="p-3">{{ item.client_name  }}</td>
                    <td class="p-3">{{ item.job_name }}</td>
                    <td class="p-3">{{ item.files_found }}</td>
                    <td class="p-3">{{ item.files_uploaded }}</td>
                    <td class="p-3">{{ item.files_skipped }}</td>
                    <td class="p-3">{{ item.errors }}</td>
                    <td class="p-3">{{ Number(item.duration).toFixed(2) }} s</td>

                    <td class="p-3">

                        <span
                            :class="item.status==='OK'
                                ? 'text-green-600 font-semibold'
                                : 'text-red-600 font-semibold'"
                        >
                            {{ item.status }}
                        </span>

                    </td>

                </tr>

                <tr v-if="history.length===0">

                    <td
                        colspan="8"
                        class="text-center py-10 text-neutral-500"
                    >

                        No existen registros.

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

    <div class="flex justify-between items-center mt-6">

        <div class="text-sm text-neutral-500">

            Mostrando

            {{ history.length }}

            registros de

            {{ total }}

        </div>

        <div class="flex items-center gap-3">

            <button
                class="border rounded-lg px-4 py-2 disabled:opacity-50"
                :disabled="page===1"
                @click="previous"
            >

                ← Anterior

            </button>

            <span>

                Página {{ page }} de {{ pages }}

            </span>

            <button
                class="border rounded-lg px-4 py-2 disabled:opacity-50"
                :disabled="page>=pages"
                @click="next"
            >

                Siguiente →

            </button>

        </div>

    </div>

</MainLayout>

</template>

<script setup>

import { ref,onMounted } from 'vue'

import MainLayout from '../components/layout/MainLayout.vue'

import {
    getHistory
} from '../api/client'

const history = ref([])

const page = ref(1)

const limit = ref(10)

const total = ref(0)

const pages = ref(0)

async function loadHistory(){

    const response = await getHistory(

        page.value,

        limit.value

    )

    history.value = response.data

    total.value = response.total

    pages.value = response.pages

}

function previous(){

    if(page.value>1){

        page.value--

        loadHistory()

    }

}

function next(){

    if(page.value<pages.value){

        page.value++

        loadHistory()

    }

}

function changeLimit(){

    page.value=1

    loadHistory()

}

onMounted(loadHistory)

</script>