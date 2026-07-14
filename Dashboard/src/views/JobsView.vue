<template>

<MainLayout>

    <div class="flex justify-between items-center mb-6">

        <div>

            <h1 class="text-3xl font-bold">
                Trabajos
            </h1>

            <p class="text-sm text-neutral-500 mt-1">
                Total de trabajos: <strong>{{ filteredJobs.length }}</strong>
            </p>

        </div>

        <div class="flex gap-3">

            <button
                @click="loadJobs"
                class="bg-gray-200 hover:bg-gray-300 px-4 py-3 rounded-lg"
            >
                Actualizar
            </button>

            <button
                @click="newJob"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg"
            >
                + Nuevo trabajo
            </button>

        </div>

    </div>

    <div class="bg-white border rounded-xl shadow-sm p-4 mb-6">

        <input
            v-model="search"
            type="text"
            placeholder="Buscar por nombre o conexión..."
            class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring focus:ring-blue-200"
        >

    </div>

    <JobDialog
        v-model="showDialog"
        :job="selectedJob"
        @saved="jobSaved"
    />

    <JobTable
        :jobs="filteredJobs"
        @edit="editJob"
        @delete="removeJob"
        @run="runJob"
    />

    <div class="mt-4 text-sm text-neutral-500 text-right">

        Última actualización:
        {{ lastUpdate }}

    </div>

</MainLayout>

</template>

<script setup>

import {

    ref,
    computed,
    onMounted,
    onUnmounted

} from 'vue'

import MainLayout from '../components/layout/MainLayout.vue'
import JobDialog from '../components/dialogs/JobDialog.vue'
import JobTable from '../components/jobs/JobTable.vue'

import {

    getJobs,
    deleteJob,
    runJob as executeJob

} from '../api/client'

const jobs = ref([])

const search = ref('')

const lastUpdate = ref('-')

const showDialog = ref(false)

let timer = null

const selectedJob = ref({

    id:null,
    name:'',
    source:'',
    destination:'',
    schedule:''

})

const filteredJobs = computed(()=>{

    if(search.value.trim()===''){

        return jobs.value

    }

    return jobs.value.filter(job=>{

        return (

            job.name
                ?.toLowerCase()
                .includes(search.value.toLowerCase())

            ||

            job.connection
                ?.toLowerCase()
                .includes(search.value.toLowerCase())

        )

    })

})

async function loadJobs(){

    const response = await getJobs()

    jobs.value = response.data

    lastUpdate.value = new Date().toLocaleTimeString()

}

function jobSaved(){

    showDialog.value=false

    loadJobs()

}

function newJob(){

    selectedJob.value={

        id:null,
        name:'',
        source:'',
        destination:'',
        schedule:''

    }

    showDialog.value=true

}

function editJob(job){

    selectedJob.value={

        ...job

    }

    showDialog.value=true

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

    await loadJobs()

}

onMounted(()=>{

    loadJobs()

    timer=setInterval(loadJobs,5000)

})

onUnmounted(()=>{

    clearInterval(timer)

})

</script>