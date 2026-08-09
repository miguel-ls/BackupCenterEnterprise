<template>

    <MainLayout>

        <div class="p-6">

            <h1 class="text-2xl font-bold mb-6">
                Transferencias
            </h1>

            <!-- FILTROS -->
            <div class="bg-white rounded-xl border shadow-sm mb-6">
                <div class="p-4 border-b font-semibold">
                    Filtros
                </div>

                <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Estado
                        </label>
                        <select
                            v-model="filters.status"
                            @change="load(true)"
                            class="w-full border rounded-lg px-3 py-2"
                        >
                            <option value="all">Todos</option>
                            <option value="uploading">Subiendo</option>
                            <option value="completed">Completado</option>
                            <option value="failed">Error</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Fecha
                        </label>
                        <select
                            v-model="filters.date"
                            @change="load(true)"
                            class="w-full border rounded-lg px-3 py-2"
                        >
                            <option value="all">Todas</option>
                            <option value="today">Hoy</option>
                            <option value="yesterday">Ayer</option>
                            <option value="7days">Últimos 7 días</option>
                            <option value="30days">Últimos 30 días</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Cliente
                        </label>
                        <select
                            v-model="filters.client"
                            @change="load(true)"
                            class="w-full border rounded-lg px-3 py-2"
                        >
                            <option value="all">Todos</option>

                            <option
                                v-for="c in clients"
                                :key="c"
                                :value="c"
                            >
                                {{ c }}
                            </option>

                        </select>
                    </div>                    

                </div>
            </div>

            <!-- RESUMEN -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                <div class="bg-white rounded-xl border shadow-sm p-4">
                    <div class="text-sm text-gray-500">Total</div>
                    <div class="text-2xl font-bold mt-1">
                        {{ summary.total }}
                    </div>
                </div>

                <div class="bg-white rounded-xl border shadow-sm p-4">
                    <div class="text-sm text-gray-500">Subiendo</div>
                    <div class="text-2xl font-bold text-blue-600 mt-1">
                        {{ summary.uploading }}
                    </div>
                </div>

                <div class="bg-white rounded-xl border shadow-sm p-4">
                    <div class="text-sm text-gray-500">Completados</div>
                    <div class="text-2xl font-bold text-green-600 mt-1">
                        {{ summary.completed }}
                    </div>
                </div>

                <div class="bg-white rounded-xl border shadow-sm p-4">
                    <div class="text-sm text-gray-500">Errores</div>
                    <div class="text-2xl font-bold text-red-600 mt-1">
                        {{ summary.failed }}
                    </div>
                </div>

            </div>

            <!-- TABLA PRO -->
            <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

                <div class="p-4 border-b font-semibold">
                    Archivos
                </div>

                <div v-if="loading" class="p-6 text-center text-gray-500">
                    Cargando...
                </div>

                <div v-else>

                    <div v-if="items.length === 0" class="p-6 text-center text-gray-500">
                        No hay transferencias para los filtros seleccionados.
                    </div>

                    <div v-else class="overflow-x-auto">

                        <table class="min-w-full text-sm">

                            <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                                <tr>
                                    <th class="px-4 py-3 text-left">Archivo</th>
                                    <th class="px-4 py-3 text-left">Cliente / Job</th>
                                    <th class="px-4 py-3 text-left">Progreso</th>
                                    <th class="px-4 py-3 text-left">Transferido</th>
                                    <th class="px-4 py-3 text-left">Velocidad</th>
                                    <th class="px-4 py-3 text-left">Estado</th>
                                    <th class="px-4 py-3 text-left">Actualizado</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="item in items"
                                    :key="item.job_id + '-' + item.file_name"
                                    class="border-t hover:bg-gray-50 transition"
                                >

                                    <!-- Archivo -->
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ item.file_name }}
                                    </td>

                                    <!-- Cliente -->
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        {{ item.client_name }}<br>
                                        <span class="text-gray-400">
                                            {{ item.job_name }}
                                        </span>
                                    </td>

                                    <!-- Progreso -->
                                    <td class="px-4 py-3 w-48">
                                        <div class="w-full bg-gray-200 rounded h-2">
                                            <div
                                                class="h-2 rounded transition-all duration-500"
                                                :class="getProgressClass(item.status)"
                                                :style="{ width: getProgress(item) + '%' }"
                                            ></div>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ getProgress(item) }}%
                                        </div>
                                    </td>

                                    <!-- Bytes -->
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        {{ formatBytes(item.uploaded_bytes) }}
                                        /
                                        {{ formatBytes(item.total_bytes) }}
                                    </td>

                                    <!-- Velocidad -->
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        {{ formatSpeed(item.speed) }}
                                    </td>

                                    <!-- Estado -->
                                    <td class="px-4 py-3">
                                        <span
                                            class="px-2 py-1 rounded text-xs font-semibold"
                                            :class="{
                                                'bg-blue-100 text-blue-600': item.status === 'uploading',
                                                'bg-green-100 text-green-600': item.status === 'completed',
                                                'bg-red-100 text-red-600': item.status === 'failed'
                                            }"
                                        >
                                            {{ getStatus(item.status) }}
                                        </span>
                                    </td>

                                    <!-- Fecha -->
                                    <td class="px-4 py-3 text-xs text-gray-400">
                                        {{ formatDate(item.updated_at) }}
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </MainLayout>

</template>


<script setup>

import {
    ref,
    onMounted,
    onUnmounted
} from "vue"

import MainLayout from "../components/layout/MainLayout.vue"
import API from "../config/api"

const items = ref([])

const clients = ref([])

const loading = ref(false)


const summary = ref({

    total: 0,

    uploading: 0,

    completed: 0,

    failed: 0

})


const filters = ref({
    status: "all",
    date: "all",
    client: "all"
})


let timer = null

let requestInProgress = false


async function load(showLoading = false) {

    /*
     * Solo mostramos "Cargando..." en la carga inicial
     * o cuando el usuario cambia los filtros.
     *
     * Las actualizaciones automáticas NO muestran loading,
     * evitando el parpadeo de la pantalla.
     */

    if (showLoading) {
        loading.value = true
    }


    /*
     * Evita que una consulta se ejecute encima de otra
     * si la respuesta tarda más que el intervalo.
     */

    if (requestInProgress) {
        return
    }


    requestInProgress = true


    try {

        const params = new URLSearchParams({
            status: filters.value.status,
            date: filters.value.date,
            client: filters.value.client
        })

        const res = await fetch(
            `${API}/progress.php?${params.toString()}`,
            {
                cache: "no-store"
            }
        )


        if (!res.ok) {

            throw new Error(
                `HTTP ${res.status}`
            )

        }


        const json = await res.json()


        /*
         * Actualizamos los datos directamente.
         *
         * Como las filas mantienen su :key,
         * Vue actualiza solamente los elementos que cambiaron.
         */

        items.value = json.data || []

        // generar lista de clientes única
        const uniqueClients = new Set()

        items.value.forEach(i => {
            if (i.client_name) {
                uniqueClients.add(i.client_name)
            }
        })

        // Solo llenar clientes la primera vez (no perder opciones al filtrar)
        if (clients.value.length === 0) {
            clients.value = Array.from(uniqueClients).sort()
        }

        summary.value = json.summary || {

            total: 0,

            uploading: 0,

            completed: 0,

            failed: 0

        }


    } catch (e) {

        console.error(
            "Error cargando transferencias:",
            e
        )

    } finally {

        requestInProgress = false

        if (showLoading) {
            loading.value = false
        }

    }

}


function getProgress(item) {

    const total = Number(
        item.total_bytes
    )

    const uploaded = Number(
        item.uploaded_bytes
    )


    if (
        !Number.isFinite(total) ||
        total <= 0
    ) {

        return 0

    }


    if (
        !Number.isFinite(uploaded) ||
        uploaded <= 0
    ) {

        return 0

    }


    /*
     * Nunca permitimos que visualmente supere 100%.
     */

    const safeUploaded = Math.min(
        uploaded,
        total
    )


    const progress =
        (safeUploaded / total) * 100


    return Math.min(
        100,
        Math.round(progress)
    )

}


function formatBytes(bytes) {

    const value = Number(bytes)


    if (
        !Number.isFinite(value) ||
        value <= 0
    ) {

        return "0 B"

    }


    const sizes = [

        "B",
        "KB",
        "MB",
        "GB",
        "TB"

    ]


    const i = Math.min(

        Math.floor(
            Math.log(value) /
            Math.log(1024)
        ),

        sizes.length - 1

    )


    return (

        value /
        Math.pow(1024, i)

    ).toFixed(1)

        + " " +

        sizes[i]

}


function formatSpeed(bytesPerSecond) {

    const value = Number(
        bytesPerSecond
    )


    if (
        !Number.isFinite(value) ||
        value <= 0
    ) {

        return "0 MB/s"

    }


    const mbPerSecond =
        value /
        (1024 * 1024)


    return (

        mbPerSecond.toFixed(2)

        +

        " MB/s"

    )

}


function getStatus(status) {

    switch (status) {

        case "uploading":

            return "Subiendo"


        case "completed":

            return "Completado"


        case "failed":

            return "Error"


        default:

            return status || "Desconocido"

    }

}


function getProgressClass(status) {

    switch (status) {

        case "completed":

            return "bg-green-600"


        case "failed":

            return "bg-red-600"


        case "uploading":

        default:

            return "bg-blue-600"

    }

}


function formatDate(date) {

    if (!date) {

        return "-"

    }


    const value =
        new Date(
            date.replace(
                " ",
                "T"
            )
        )


    if (
        isNaN(
            value.getTime()
        )
    ) {

        return date

    }


    return value.toLocaleString()

}


onMounted(() => {

    /*
     * Primera carga.
     */

    load(true)


    /*
     * Actualización automática.
     *
     * IMPORTANTE:
     * aquí NO usamos load(true).
     */

    timer = setInterval(

        () => load(false),

        2000

    )

})


onUnmounted(() => {

    clearInterval(timer)

})

</script>