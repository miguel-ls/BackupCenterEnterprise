<template>

<MainLayout>

    <div class="flex justify-between items-center mb-8">

        <h1 class="text-3xl font-bold">

            Historial

        </h1>

        <button
            @click="loadHistory"
            class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700"
        >
            Actualizar
        </button>

    </div>

    <div
        class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden"
    >

        <table class="w-full">

            <thead class="bg-neutral-100">

                <tr>

                    <th class="text-left p-3">Fecha</th>
                    <th class="text-left p-3">Cliente</th>
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
                    class="border-t"
                >

                    <td class="p-3">{{ item.executed_at }}</td>
                    <td class="p-3">{{ item.client }}</td>
                    <td class="p-3">{{ item.files_found }}</td>
                    <td class="p-3">{{ item.files_uploaded }}</td>
                    <td class="p-3">{{ item.files_skipped }}</td>
                    <td class="p-3">{{ item.errors }}</td>
                    <td class="p-3">{{ item.duration }} s</td>

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

            </tbody>

        </table>

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

async function loadHistory(){

    const response = await getHistory()

    history.value = response.data

}

onMounted(loadHistory)

</script>