<template>

<MainLayout>

<div class="flex justify-between items-center mb-8">

    <h1 class="text-3xl font-bold">

        Trabajos

    </h1>

    <button

        @click="showDialog=true"

        class="bg-blue-600 text-white px-5 py-3 rounded-lg"

    >

        + Nuevo trabajo

    </button>

</div>

<JobDialog
    v-model="showDialog"
    @saved="jobSaved"
/>

    <div class="bg-white rounded-xl border border-neutral-200 shadow-sm">

<!--
<JobForm />

<div class="h-6"></div>
-->

        <table class="w-full">

            <thead class="bg-neutral-50">

                <tr>

                    <th class="text-left p-4">ID</th>

                    <th class="text-left p-4">Trabajo</th>

                    <th class="text-left p-4">Estado</th>

<th class="text-left p-4">Hora</th>

<th class="text-center p-4">
    Acciones
</th>

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

<td class="p-4">

<div class="flex justify-center gap-2">

    <button
        class="p-2 rounded bg-green-600 text-white hover:bg-green-700"
    >
        <Play :size="18"/>
    </button>

    <button
        class="p-2 rounded bg-amber-500 text-white hover:bg-amber-600"
    >
        <Pencil :size="18"/>
    </button>

    <button
        @click="removeJob(job.id)"
        class="p-2 rounded bg-red-600 text-white hover:bg-red-700"
    >
        <Trash2 :size="18"/>
    </button>

</div>

</td>

                </tr>

            </tbody>

        </table>

    </div>

</MainLayout>

</template>

<script setup>

import { ref, onMounted } from 'vue'

import MainLayout from '../components/layout/MainLayout.vue'
import JobDialog from '../components/dialogs/JobDialog.vue'

import { getJobs, deleteJob } from '../api/client'

import {
    Play,
    Pencil,
    Trash2
} from 'lucide-vue-next'

const jobs = ref([])

const showDialog = ref(false)

async function loadJobs(){

    jobs.value = await getJobs()

}

function jobSaved(){

    showDialog.value = false

    loadJobs()

}

async function removeJob(id){

    if(!confirm('¿Eliminar este trabajo?')){
        return
    }

    await deleteJob(id)

    loadJobs()

}

onMounted(loadJobs)

</script>