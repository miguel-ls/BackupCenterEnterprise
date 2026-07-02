<template>

<MainLayout>

    <h1 class="text-3xl font-bold mb-8">

        Dashboard

    </h1>

    <div class="grid grid-cols-4 gap-6">

        <StatCard
            title="Versión"
            :value="status.version"
        />

        <StatCard
            title="Servicio"
            :value="status.service"
        />

        <StatCard
            title="PHP"
            :value="status.php"
        />

        <StatCard
            title="Base de datos"
            :value="status.database ? 'OK' : 'ERROR'"
        />

    </div>

<div class="grid grid-cols-3 gap-6 mt-8">

    <div class="col-span-2 bg-white rounded-xl border border-neutral-200 shadow-sm">

        <div class="p-5 border-b">

            <h2 class="text-lg font-semibold">
                Últimos trabajos
            </h2>

        </div>

        <table class="w-full">

            <thead class="bg-neutral-50">

                <tr>

                    <th class="text-left p-4">Trabajo</th>

                    <th class="text-left p-4">Estado</th>

                    <th class="text-left p-4">Hora</th>

                </tr>

            </thead>

            <tbody>

                <tr class="border-t">

                    <td class="p-4">ERP SQL</td>

                    <td class="p-4 text-green-600 font-semibold">Correcto</td>

                    <td class="p-4">07:30</td>

                </tr>

                <tr class="border-t">

                    <td class="p-4">NAS Principal</td>

                    <td class="p-4 text-green-600 font-semibold">Correcto</td>

                    <td class="p-4">06:45</td>

                </tr>

                <tr class="border-t">

                    <td class="p-4">Documentos</td>

                    <td class="p-4 text-red-600 font-semibold">Error</td>

                    <td class="p-4">06:10</td>

                </tr>

            </tbody>

        </table>

    </div>

    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-5">

        <h2 class="text-lg font-semibold mb-4">
            Estado
        </h2>

        <div class="space-y-3">

            <div class="flex justify-between">
                <span>Servicio</span>
                <span class="text-green-600 font-semibold">{{ status.service }}</span>
            </div>

            <div class="flex justify-between">
                <span>Versión</span>
                <span>{{ status.version }}</span>
            </div>

            <div class="flex justify-between">
                <span>PHP</span>
                <span>{{ status.php }}</span>
            </div>

            <div class="flex justify-between">
                <span>Hora</span>
                <span>{{ status.time }}</span>
            </div>

        </div>

    </div>

</div>    

</MainLayout>

</template>

<script setup>

import { onMounted, ref } from 'vue'

import MainLayout from '../components/layout/MainLayout.vue'
import StatCard from '../components/cards/StatCard.vue'

import { getStatus } from '../api/client'

const status = ref({

    version:'...',

    service:'...',

    php:'...',

    database:false

})

onMounted(async()=>{

    status.value = await getStatus()

})

</script>