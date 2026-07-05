<template>

<div class="space-y-4">

    <input
        v-model="connection.name"
        placeholder="Nombre de la conexión"
        class="w-full border rounded-lg p-3"
    >

    <input
        v-model="connection.host"
        placeholder="Host"
        class="w-full border rounded-lg p-3"
    >

    <input
        v-model="connection.port"
        type="number"
        placeholder="Puerto"
        class="w-full border rounded-lg p-3"
    >

    <input
        v-model="connection.username"
        placeholder="Usuario"
        class="w-full border rounded-lg p-3"
    >

    <input
        v-model="connection.password"
        type="password"
        placeholder="Contraseña"
        class="w-full border rounded-lg p-3"
    >

    <input
        v-model="connection.hostkey"
        placeholder="Host Key"
        class="w-full border rounded-lg p-3"
    >

    <select
        v-model="connection.protocol"
        class="w-full border rounded-lg p-3"
    >
        <option>SFTP</option>
        <option>FTP</option>
    </select>

    <input
        v-model="connection.remote_path"
        placeholder="Ruta remota"
        class="w-full border rounded-lg p-3"
    >

    <div class="flex justify-end">

        <button
            @click="save"
            class="bg-blue-600 text-white px-5 py-3 rounded-lg"
        >

            Guardar

        </button>

    </div>

</div>

</template>

<script setup>

import { reactive } from 'vue'

import {
    createConnection
} from '../../api/client'

const emit = defineEmits([
    'saved'
])

const connection = reactive({

    name:'',
    host:'',
    port:22,
    username:'',
    password:'',
    hostkey:'',
    protocol:'SFTP',
    remote_path:''

})

async function save(){

    await createConnection(connection)

    emit('saved')

}

</script>