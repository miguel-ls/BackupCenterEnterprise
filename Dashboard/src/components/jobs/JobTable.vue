<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">

    <!-- Encabezado -->

    <div class="flex items-center justify-between px-6 py-4 border-b bg-neutral-50">

        <h2 class="text-lg font-bold">
            Trabajos
        </h2>

        <span class="text-sm text-neutral-500">
            Total: {{ jobs.length }}
        </span>

    </div>

    <table class="w-full">

        <thead class="bg-neutral-100">

            <tr>

                <th class="text-left px-4 py-3 font-semibold">ID</th>

                <th class="text-left px-4 py-3 font-semibold">
                    Trabajo
                </th>

                <th class="text-left px-4 py-3 font-semibold">
                    Conexión
                </th>

                <th class="text-center px-4 py-3 font-semibold">
                    Estado
                </th>

                <th class="text-left px-4 py-3 font-semibold">
                    Última ejecución
                </th>

                <th class="text-center px-4 py-3 font-semibold">
                    Acciones
                </th>

            </tr>

        </thead>

        <tbody v-if="jobs.length">

            <tr
                v-for="job in jobs"
                :key="job.id"
                class="border-t hover:bg-blue-50 transition-colors"
            >

                <td class="px-4 py-3 font-semibold text-neutral-700">
                    {{ job.id }}
                </td>

                <td class="px-4 py-3">

                    <div class="font-semibold">
                        {{ job.name }}
                    </div>

                </td>

                <td class="px-4 py-3 text-neutral-600">
                    {{ job.connection }}
                </td>

                <td class="px-4 py-3 text-center">

                    <span
                        class="px-3 py-1 rounded-full text-xs font-bold"

                        :class="{

                            'bg-yellow-100 text-yellow-700':
                                job.status=='Pending',

                            'bg-blue-100 text-blue-700':
                                job.status=='Running' ||
                                job.status=='En ejecución',

                            'bg-green-100 text-green-700':
                                job.status=='Completed' ||
                                job.status=='Correcto',

                            'bg-red-100 text-red-700':
                                job.status=='Failed' ||
                                job.status=='Error'

                        }"
                    >

                        {{ job.status }}

                    </span>

                </td>

                <td class="px-4 py-3 text-sm text-neutral-600">

                    {{ job.time }}

                </td>

                <td class="px-4 py-3">

                    <div class="flex justify-center gap-2">

                        <button
                            @click="$emit('run',job)"
                            class="w-9 h-9 rounded-lg bg-green-600 hover:bg-green-700 text-white flex items-center justify-center transition"
                            title="Ejecutar"
                        >
                            <Play :size="17"/>
                        </button>

                        <button
                            @click="$emit('edit',job)"
                            class="w-9 h-9 rounded-lg bg-amber-500 hover:bg-amber-600 text-white flex items-center justify-center transition"
                            title="Editar"
                        >
                            <Pencil :size="17"/>
                        </button>

                        <button
                            @click="$emit('delete',job.id)"
                            class="w-9 h-9 rounded-lg bg-red-600 hover:bg-red-700 text-white flex items-center justify-center transition"
                            title="Eliminar"
                        >
                            <Trash2 :size="17"/>
                        </button>

                    </div>

                </td>

            </tr>

        </tbody>

        <tbody v-else>

            <tr>

                <td
                    colspan="6"
                    class="text-center py-10 text-neutral-500"
                >

                    No existen trabajos registrados.

                </td>

            </tr>

        </tbody>

    </table>

</div>

</template>

<script setup>

import {

    Play,
    Pencil,
    Trash2

} from "lucide-vue-next";

defineProps({

    jobs:{
        type:Array,
        default:()=>[]
    }

});

defineEmits([

    "run",
    "edit",
    "delete"

]);

</script>