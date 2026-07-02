<template>

<MainLayout>

    <h1 class="text-3xl font-bold mb-8">

        Trabajos

    </h1>

    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm">

        <table class="w-full">

            <thead class="bg-neutral-50">

                <tr>

                    <th class="text-left p-4">ID</th>

                    <th class="text-left p-4">Trabajo</th>

                    <th class="text-left p-4">Estado</th>

                    <th class="text-left p-4">Hora</th>

                </tr>

            </thead>

            <tbody>

                <tr
                    v-for="job in jobs"
                    :key="job.id"
                    class="border-t"
                >

                    <td class="p-4">{{ job.id }}</td>

                    <td class="p-4">{{ job.name }}</td>

                    <td
                        class="p-4 font-semibold"
                        :class="job.status=='Correcto'
                            ? 'text-green-600'
                            : 'text-red-600'"
                    >
                        {{ job.status }}
                    </td>

                    <td class="p-4">
                        {{ job.time }}
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

import { getJobs } from '../api/client'

const jobs = ref([])

onMounted(async()=>{

    jobs.value = await getJobs()

})

</script>