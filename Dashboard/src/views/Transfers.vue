<template>

    <MainLayout>

        <div class="p-6">

            <h1 class="text-2xl font-bold mb-6">
                Transferencias
            </h1>


            <!-- ================================================= -->
            <!-- FILTROS -->
            <!-- ================================================= -->

            <div class="bg-white rounded-xl border shadow-sm mb-6">

                <div class="p-4 border-b font-semibold">
                    Filtros
                </div>

                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Estado -->

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Estado
                        </label>

                        <select
                            v-model="filters.status"
                            @change="load(true)"
                            class="w-full border rounded-lg px-3 py-2"
                        >

                            <option value="all">
                                Todos
                            </option>

                            <option value="uploading">
                                Subiendo
                            </option>

                            <option value="completed">
                                Completado
                            </option>

                            <option value="failed">
                                Error
                            </option>

                        </select>

                    </div>


                    <!-- Fecha -->

                    <div>

                        <label class="block text-sm font-medium mb-1">
                            Fecha
                        </label>

                        <select
                            v-model="filters.date"
                            @change="load(true)"
                            class="w-full border rounded-lg px-3 py-2"
                        >

                            <option value="all">
                                Todas
                            </option>

                            <option value="today">
                                Hoy
                            </option>

                            <option value="yesterday">
                                Ayer
                            </option>

                            <option value="7days">
                                Últimos 7 días
                            </option>

                            <option value="30days">
                                Últimos 30 días
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- RESUMEN -->
            <!-- ================================================= -->

            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6"
            >

                <!-- Total -->

                <div class="bg-white rounded-xl border shadow-sm p-4">

                    <div class="text-sm text-gray-500">
                        Total
                    </div>

                    <div class="text-2xl font-bold mt-1">
                        {{ summary.total }}
                    </div>

                </div>


                <!-- Subiendo -->

                <div class="bg-white rounded-xl border shadow-sm p-4">

                    <div class="text-sm text-gray-500">
                        Subiendo
                    </div>

                    <div class="text-2xl font-bold text-blue-600 mt-1">
                        {{ summary.uploading }}
                    </div>

                </div>


                <!-- Completados -->

                <div class="bg-white rounded-xl border shadow-sm p-4">

                    <div class="text-sm text-gray-500">
                        Completados
                    </div>

                    <div class="text-2xl font-bold text-green-600 mt-1">
                        {{ summary.completed }}
                    </div>

                </div>


                <!-- Errores -->

                <div class="bg-white rounded-xl border shadow-sm p-4">

                    <div class="text-sm text-gray-500">
                        Errores
                    </div>

                    <div class="text-2xl font-bold text-red-600 mt-1">
                        {{ summary.failed }}
                    </div>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- TRANSFERENCIAS -->
            <!-- ================================================= -->

            <div class="bg-white rounded-xl border shadow-sm">

                <div class="p-4 border-b font-semibold">
                    Archivos
                </div>


                <!-- CARGA INICIAL -->

                <div
                    v-if="loading"
                    class="p-6 text-center text-gray-500"
                >
                    Cargando...
                </div>


                <div v-else>

                    <div
                        v-if="items.length === 0"
                        class="p-6 text-center text-gray-500"
                    >
                        No hay transferencias para los filtros seleccionados.
                    </div>


                    <div
                        v-for="item in items"
                        :key="item.job_id + '-' + item.file_name"
                        class="p-4 border-b"
                    >

                        <!-- Cabecera -->

                        <div class="flex justify-between text-sm mb-1">

                            <div>

                                <strong>
                                    {{ item.file_name }}
                                </strong>

                                <div class="text-gray-500 text-xs">

                                    {{ item.client_name }}

                                    /

                                    {{ item.job_name }}

                                </div>

                            </div>


                            <div class="text-right text-xs">

                                <div class="font-semibold">
                                    {{ getProgress(item) }}%
                                </div>

                                <div class="text-gray-500">
                                    {{ getStatus(item.status) }}
                                </div>

                            </div>

                        </div>


                        <!-- Barra -->

                        <div class="w-full bg-gray-200 rounded h-3">

                            <div
                                class="h-3 rounded transition-all duration-500"
                                :class="getProgressClass(item.status)"
                                :style="{
                                    width: getProgress(item) + '%'
                                }"
                            ></div>

                        </div>


                        <!-- Información -->

                        <div
                            class="flex justify-between text-xs text-gray-500 mt-1"
                        >

                            <div>

                                {{ formatBytes(item.uploaded_bytes) }}

                                /

                                {{ formatBytes(item.total_bytes) }}

                            </div>


                            <div>

                                {{ formatSpeed(item.speed) }}

                            </div>

                        </div>


                        <!-- Fecha -->

                        <div
                            class="text-xs text-gray-400 mt-2"
                        >

                            Actualizado:

                            {{ formatDate(item.updated_at) }}

                        </div>

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


const items = ref([])

const loading = ref(false)


const summary = ref({

    total: 0,

    uploading: 0,

    completed: 0,

    failed: 0

})


const filters = ref({

    status: "all",

    date: "all"

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

            date: filters.value.date

        })


        const res = await fetch(
            `/api/progress.php?${params.toString()}`,
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