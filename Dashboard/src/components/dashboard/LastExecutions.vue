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

    <table class="w-full">

        <thead class="bg-neutral-50">

            <tr>

                <th class="text-left p-3">Fecha</th>
                <th class="text-left p-3">Cliente</th>
                <th class="text-left p-3">Subidos</th>
                <th class="text-left p-3">Errores</th>
                <th class="text-left p-3">Tiempo</th>
                <th class="text-left p-3">Estado</th>

            </tr>

        </thead>

        <tbody>

            <tr
                v-for="item in history"
                :key="item.id"
                class="border-t"
            >

                <td class="p-3">
                    {{ item.executed_at }}
                </td>

                <td class="p-3">
                    {{ item.client }}
                </td>

                <td class="p-3">
                    {{ item.files_uploaded }}
                </td>

                <td class="p-3">
                    {{ item.errors }}
                </td>

                <td class="p-3">
                    {{ item.duration }} s
                </td>

                <td class="p-3">

                    <span
                        :class="item.status === 'OK'
                            ? 'text-green-600 font-semibold'
                            : 'text-red-600 font-semibold'"
                    >
                        {{ item.status }}
                    </span>

                </td>

            </tr>

            <tr v-if="history.length === 0">

                <td
                    colspan="6"
                    class="text-center p-8 text-neutral-500"
                >

                    No existen ejecuciones.

                </td>

            </tr>

        </tbody>

    </table>

</div>

</template>

<script setup>

import { ref,onMounted } from 'vue'

import { getHistory } from '../../api/client'

const history = ref([])

async function load(){

    const response = await getHistory()

    history.value = response.data

}

onMounted(load)

</script>