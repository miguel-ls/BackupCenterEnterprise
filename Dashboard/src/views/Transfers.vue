<template>

<div class="p-6">

    <h1 class="text-2xl font-bold mb-6">
        Transferencias en tiempo real
    </h1>

    <div class="bg-white rounded-xl border shadow-sm">

        <div class="p-4 border-b font-semibold">
            Archivos en transferencia
        </div>

        <div v-if="loading" class="p-6 text-center text-gray-500">
            Cargando...
        </div>

        <div v-else>

            <div
                v-if="items.length === 0"
                class="p-6 text-center text-gray-500"
            >
                No hay transferencias activas
            </div>

            <div
                v-for="item in items"
                :key="item.job_id + item.file_name"
                class="p-4 border-b"
            >

                <div class="flex justify-between text-sm mb-1">

                    <div>
                        <strong>{{ item.file_name }}</strong>
                        <div class="text-gray-500 text-xs">
                            {{ item.client_name }} / {{ item.job_name }}
                        </div>
                    </div>

                    <div class="text-right text-xs text-gray-500">
                        {{ getProgress(item) }}%
                    </div>

                </div>

                <!-- barra -->
                <div class="w-full bg-gray-200 rounded h-3">

                    <div
                        class="bg-blue-600 h-3 rounded transition-all duration-500"
                        :style="{ width: getProgress(item) + '%' }"
                    ></div>

                </div>

                <div class="flex justify-between text-xs text-gray-500 mt-1">

                    <div>
                        {{ formatBytes(item.uploaded_bytes) }} /
                        {{ formatBytes(item.total_bytes) }}
                    </div>

                    <div>
                        {{ item.speed }} MB/s
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</template>

<script setup>

import { ref, onMounted, onUnmounted } from "vue"

const items = ref([])
const loading = ref(false)

let timer = null

async function load() {

    loading.value = true

    try {

        const res = await fetch("/api/progress.php")

        const json = await res.json()

        items.value = json.data || []

    } catch (e) {

        console.error(e)

        items.value = []

    }

    loading.value = false

}

function getProgress(item) {

    if (!item.total_bytes) return 0

    return Math.round(
        (item.uploaded_bytes / item.total_bytes) * 100
    )

}

function formatBytes(bytes) {

    if (!bytes) return "0 B"

    const sizes = ["B", "KB", "MB", "GB"]

    const i = Math.floor(Math.log(bytes) / Math.log(1024))

    return (bytes / Math.pow(1024, i)).toFixed(1) + " " + sizes[i]

}

onMounted(() => {

    load()

    timer = setInterval(load, 2000)

})

onUnmounted(() => {

    clearInterval(timer)

})

</script>
