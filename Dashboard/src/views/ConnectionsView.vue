<template>

<MainLayout>

    <div class="flex justify-between items-center mb-8">

        <h1 class="text-3xl font-bold">

            Conexiones

        </h1>

        <button
            @click="showDialog = true"
            class="bg-blue-600 text-white px-5 py-3 rounded-lg"
        >
            + Nueva conexión
        </button>

    </div>

<ConnectionDialog
    v-model="showDialog"
    :connection="selectedConnection"
    @saved="connectionSaved"
/>

<ConnectionTable
    :connections="connections"
    @edit="editConnection"
    @delete="removeConnection"
/>

</MainLayout>

</template>

<script setup>

import { ref, onMounted } from 'vue'

import MainLayout from '../components/layout/MainLayout.vue'
import ConnectionDialog from '../components/dialogs/ConnectionDialog.vue'
import ConnectionTable from '../components/connections/ConnectionTable.vue'

import {
    getConnections,
    deleteConnection
} from '../api/client'

const connections = ref([])

const showDialog = ref(false)
const selectedConnection = ref(null)

async function loadConnections(){

    const response = await getConnections()

    connections.value = response.data

}

function connectionSaved(){

    showDialog.value = false

    loadConnections()

}

function editConnection(connection){

    selectedConnection.value = {
        ...connection
    }

    showDialog.value = true

}

async function removeConnection(id){

    if(!confirm('¿Eliminar esta conexión?')){
        return
    }

    await deleteConnection(id)

    await loadConnections()

}

onMounted(loadConnections)

</script>