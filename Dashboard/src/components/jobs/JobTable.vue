<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm">

    <table class="w-full">

        <thead class="bg-neutral-50">

            <tr>

                <th class="text-left p-4">ID</th>
                <th class="text-left p-4">Trabajo</th>
                <th class="text-left p-4">Conexión</th>
                <th class="text-left p-4">Estado</th>
                <th class="text-left p-4">Hora</th>
                <th class="text-center p-4">Acciones</th>

            </tr>

        </thead>

        <tbody>

            <tr
                v-for="job in jobs"
                :key="job.id"
                class="border-t"
            >

                <td class="p-4">{{ job.id }}</td>

                <td class="p-4">
                    {{ job.name }}
                </td>

                <td class="p-4">
                    {{ job.connection }}
                </td>

                <td
                    class="p-4 font-semibold"
                    :class="job.status == 'Correcto'
                        ? 'text-green-600'
                        : job.status == 'En ejecución'
                            ? 'text-blue-600'
                            : 'text-red-600'"
                >
                    {{ job.status }}
                </td>

                <td class="p-4">
                    {{ job.time }}
                </td>

                <td class="p-4">

                    <div class="flex justify-center gap-2">

                        <button
                            @click="$emit('run', job)"
                            class="p-2 rounded bg-green-600 text-white hover:bg-green-700"
                            title="Ejecutar"
                        >
                            <Play :size="18"/>
                        </button>

                        <button
                            @click="$emit('edit', job)"
                            class="p-2 rounded bg-amber-500 text-white hover:bg-amber-600"
                            title="Editar"
                        >
                            <Pencil :size="18"/>
                        </button>

                        <button
                            @click="$emit('delete', job.id)"
                            class="p-2 rounded bg-red-600 text-white hover:bg-red-700"
                            title="Eliminar"
                        >
                            <Trash2 :size="18"/>
                        </button>

                    </div>

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
} from 'lucide-vue-next'

defineProps({

    jobs:Array

})

defineEmits([

    'run',
    'edit',
    'delete'

])

</script>