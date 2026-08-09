<template>

<div class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden">

    <!-- Encabezado -->

    <div class="flex items-center justify-between px-6 py-4 border-b bg-neutral-50">

        <div>

            <h2 class="text-lg font-bold">

                Estado de Trabajos

            </h2>

            <p class="text-sm text-neutral-500">

                Total registros: {{ items.length }}

            </p>

        </div>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead class="bg-slate-100">

                <tr>

                    <th class="text-left px-4 py-3 font-semibold">ID</th>

                    <th class="text-left px-4 py-3 font-semibold">Trabajo</th>

                    <th class="text-left px-4 py-3 font-semibold">Cliente</th>

                    <th class="text-center px-4 py-3 font-semibold">Estado</th>

                    <th class="text-left px-4 py-3 font-semibold">Worker</th>

                    <th class="text-center px-4 py-3 font-semibold">Intentos</th>

                    <th class="text-left px-4 py-3 font-semibold">Inicio</th>

                    <th class="text-left px-4 py-3 font-semibold">Fin</th>

                </tr>

            </thead>

            <tbody v-if="items.length">

                <tr
                    v-for="item in items"
                    :key="item.id"
                    class="border-t hover:bg-blue-50 transition-colors"
                >

                    <td class="px-4 py-3 font-semibold text-slate-700">

                        #{{ item.id }}

                    </td>

                    <td class="px-4 py-3">

                        {{ item.name }}

                    </td>

                    <td class="px-4 py-3">

                        {{ item.client_name || "-" }}

                    </td>

                    <td class="px-4 py-3 text-center">

                        <StatusBadge
                            :status="item.status"
                        />

                    </td>

                    <td class="px-4 py-3">

                        {{ item.worker || "-" }}

                    </td>

                    <td class="px-4 py-3 text-center">

                        <span
                            class="inline-flex items-center justify-center min-w-[34px] h-8 rounded-full bg-slate-100 font-semibold"
                        >

                            {{ item.attempts }}

                        </span>

                    </td>

                    <td class="px-4 py-3 text-sm text-slate-600">

                        {{ formatDate(item.started_at) }}

                    </td>

                    <td class="px-4 py-3 text-sm text-slate-600">

                        {{ formatDate(item.finished_at) }}

                    </td>

                </tr>

            </tbody>

            <tbody v-else>

                <tr>

                    <td
                        colspan="8"
                        class="text-center py-12 text-neutral-500"
                    >

                        No existen trabajos en la cola.

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

</template>

<script setup>

import StatusBadge from "@/components/common/StatusBadge.vue"

defineProps({

    items:{
        type:Array,
        default:()=>[]
    }

})

function formatDate(value) {

    if (!value) {

        return "-"

    }

    return new Date(value).toLocaleString(
        "es-PE",
        {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit",
            hour12: false
        }
    )

}

</script>