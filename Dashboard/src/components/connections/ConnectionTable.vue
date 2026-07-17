<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm">

    <table class="w-full">

        <thead class="bg-neutral-50">

            <tr>

                <th class="text-left p-4">Host</th>
                <th class="text-left p-4">Puerto</th>
                <th class="text-left p-4">Usuario</th>
                <th class="text-left p-4">Protocolo</th>
                <th class="text-center p-4">Acciones</th>

            </tr>

        </thead>

        <tbody>

            <tr
                v-for="connection in connections"
                :key="connection.id"
                class="border-t"
            >

                <td class="p-4">
                    {{ connection.host }}
                </td>

                <td class="p-4">
                    {{ connection.port }}
                </td>

                <td class="p-4">
                    {{ connection.username }}
                </td>

                <td class="p-4">
                    {{ connection.protocol }}
                </td>

                <td class="p-4">

                    <div class="flex justify-center gap-2">

                        <button
                            @click="$emit('edit',connection)"
                            class="bg-amber-500 hover:bg-amber-600 text-white rounded p-2"
                        >
                            <Pencil :size="17"/>
                        </button>

                        <button
                            @click="$emit('delete',connection.id)"
                            class="bg-red-600 hover:bg-red-700 text-white rounded p-2"
                        >
                            <Trash2 :size="17"/>
                        </button>

                    </div>

                </td>

            </tr>

            <tr v-if="connections.length===0">

                <td
                    colspan="5"
                    class="text-center p-10 text-neutral-400"
                >

                    No existen conexiones.

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

    connections:{
        type:Array,
        default:()=>[]
    }

})

defineEmits([

    'edit',
    'delete'

])

</script>