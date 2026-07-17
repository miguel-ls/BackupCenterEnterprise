<template>

<MainLayout>

    <div class="p-6">

        <div class="flex items-center justify-between mb-6">

            <div>

                <h1 class="text-2xl font-bold">
                    Clientes
                </h1>

                <p class="text-sm text-neutral-500">
                    Administración de clientes
                </p>

            </div>

<button
    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
    @click="newClient"
>
    Nuevo Cliente
</button>

        </div>

        <div class="bg-white rounded-xl shadow border overflow-hidden">

            <table class="w-full">

                <thead class="bg-neutral-100">

                    <tr>

                        <th class="text-left p-3">Código</th>
                        <th class="text-left p-3">Razón Social</th>
                        <th class="text-left p-3">Contacto</th>
                        <th class="text-left p-3">Correo</th>
                        <th class="text-center p-3">Estado</th>
                        <th class="text-center p-3">Acciones</th>

                    </tr>

                </thead>

                <tbody>

                    <tr
                        v-for="client in clients"
                        :key="client.id"
                        class="border-t hover:bg-neutral-50"
                    >

                        <td class="p-3">
                            {{ client.code }}
                        </td>

                        <td class="p-3">
                            {{ client.business_name }}
                        </td>

                        <td class="p-3">
                            {{ client.contact_name }}
                        </td>

                        <td class="p-3">
                            {{ client.email }}
                        </td>

                        <td class="p-3 text-center">

                            <span
                                class="px-2 py-1 rounded text-xs font-semibold"
                                :class="client.status == 1
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'"
                            >

                                {{ client.status == 1 ? 'Activo' : 'Inactivo' }}

                            </span>

                        </td>

<td class="p-3 text-center">

    <button
        class="text-blue-600 hover:underline mr-3"
        @click="editClient(client)"
    >
        ✏ Editar
    </button>

    <button
        class="text-red-600 hover:underline"
        @click="removeClient(client)"
    >
        🗑 Eliminar
    </button>

</td>

                    </tr>

                    <tr v-if="clients.length === 0">

                        <td
                            colspan="6"
                            class="text-center text-neutral-500 py-10"
                        >

                            No existen clientes registrados.

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

<ClientForm

    :show="showForm"

    :client="selectedClient"

    @close="closeForm"

    @save="saveClient"

/>


</MainLayout>

</template>

<script setup>

import { ref, onMounted } from "vue"

import MainLayout from "../components/layout/MainLayout.vue"
import ClientForm from "../components/clients/ClientForm.vue"

import {

    getClients,
    createClient,
    updateClient,
    deleteClient

} from "@/api/client"

const clients = ref([])
const showForm = ref(false)
const selectedClient = ref(null)

async function loadClients() {

    try {

const response = await getClients()

clients.value = response.data

    }
    catch (error) {

        console.error(error)

    }

}

function newClient() {

    showForm.value = true

}

function editClient(client) {

    selectedClient.value = { ...client }

    showForm.value = true

}

async function removeClient(client) {

    const ok = confirm(

        `¿Desea eliminar el cliente "${client.business_name}"?`

    )

    if (!ok) {

        return

    }

    try {

        await deleteClient(client.id)

        await loadClients()

    }
    catch (error) {

        console.error(error)

        alert("No fue posible eliminar el cliente.")

    }

}

function closeForm() {

    selectedClient.value = null

    showForm.value = false

}

async function saveClient(client) {

    try {

        if (client.id) {

            await updateClient(client)

        }
        else {

            await createClient(client)

        }

        await loadClients()

        closeForm()

    }
    catch (error) {

        console.error(error)

        alert("No fue posible guardar el cliente.")

    }

}

onMounted(loadClients)

</script>