<template>

<table class="w-full border border-neutral-200 bg-white rounded-lg overflow-hidden">

    <thead class="bg-neutral-100">

        <tr>

            <th class="text-left p-3">ID</th>
            <th class="text-left p-3">Trabajo</th>
            <th class="text-left p-3">Estado</th>
            <th class="text-left p-3">Worker</th>
            <th class="text-left p-3">Intentos</th>
            <th class="text-left p-3">Inicio</th>
            <th class="text-left p-3">Fin</th>

        </tr>

    </thead>

    <tbody>

        <tr
            v-for="item in items"
            :key="item.id"
            class="border-t hover:bg-neutral-50"
        >

            <td class="p-3">{{ item.id }}</td>

            <td class="p-3">{{ item.name }}</td>

            <td class="p-3">

                <StatusBadge
                    :status="item.status"
                />

            </td>

            <td class="p-3">{{ item.worker }}</td>

            <td class="p-3">{{ item.attempts }}</td>

            <td class="p-3">{{ item.started_at }}</td>

            <td class="p-3">{{ item.finished_at }}</td>

        </tr>

    </tbody>

</table>

</template>

<script setup>

import StatusBadge from '@/components/common/StatusBadge.vue'

defineProps({

    items: {
        type: Array,
        required: true
    }

})

</script>