<template>

<MainLayout>

    <div class="flex justify-between items-center mb-8">

        <h1 class="text-3xl font-bold">

            Conexiones

        </h1>

        <button
            @click="showDialog=true"
            class="bg-blue-600 text-white px-5 py-3 rounded-lg"
        >

            + Nueva conexión

        </button>

    </div>

    <ConnectionDialog
        v-model="showDialog"
        @saved="connectionSaved"
    />

    <ConnectionTable
        :connections="connections"
        @delete="removeConnection"
    />

</MainLayout>

</template>

<script setup>

import { ref,onMounted } from 'vue'

import MainLayout from '../components/layout/MainLayout.vue'
import ConnectionDialog from '../components/dialogs/ConnectionDialog.vue'
import ConnectionTable from '../components/connections/ConnectionTable.vue'

import {
    getConnections,
    deleteConnection
} from '../api/client'

const connections=ref([])

const showDialog=ref(false)

async function loadConnections(){

    connections.value=await getConnections()

}

function connectionSaved(){

    showDialog.value=false

    loadConnections()

}

async function removeConnection(id){

    if(!confirm('¿Eliminar esta conexión?')){
        return
    }

    await deleteConnection(id)

    loadConnections()

}

onMounted(loadConnections)

</script>