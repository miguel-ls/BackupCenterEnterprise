<template>

<MainLayout>

    <div class="flex justify-between items-center mb-8">

        <h1 class="text-3xl font-bold">
            Trabajos
        </h1>

        <button
            @click="newJob"
            class="bg-blue-600 text-white px-5 py-3 rounded-lg"
        >
            + Nuevo trabajo
        </button>

    </div>

    <JobDialog
        v-model="showDialog"
        :job="selectedJob"
        @saved="jobSaved"
    />

    <JobTable
        :jobs="jobs"
        @edit="editJob"
        @delete="removeJob"
        @run="runJob"
    />

</MainLayout>

</template>

<script setup>

import { ref, onMounted } from 'vue'

import MainLayout from '../components/layout/MainLayout.vue'
import JobDialog from '../components/dialogs/JobDialog.vue'
import JobTable from '../components/jobs/JobTable.vue'

import {
    getJobs,
    deleteJob,
    runJob as executeJob
} from '../api/client'

const jobs = ref([])

const showDialog = ref(false)

const selectedJob = ref({
    id: null,
    name: '',
    source: '',
    destination: '',
    schedule: ''
})

async function loadJobs(){

    const response = await getJobs()

    jobs.value = response.data

}

function jobSaved(){

    showDialog.value = false

    loadJobs()

}

function newJob(){

    selectedJob.value = {
        id: null,
        name: '',
        source: '',
        destination: '',
        schedule: ''
    }

    showDialog.value = true

}

function editJob(job){

    selectedJob.value = {
        ...job
    }

    showDialog.value = true

}

async function removeJob(id){

    if(!confirm('¿Eliminar este trabajo?')){
        return
    }

    await deleteJob(id)

    await loadJobs()

}

async function runJob(job){

    if(!confirm(`¿Ejecutar el trabajo "${job.name}"?`)){
        return
    }

    const result = await executeJob(job.id)

    alert(result.message)

}

onMounted(loadJobs)

</script>