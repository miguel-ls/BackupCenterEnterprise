<template>
<div class="bg-white rounded-xl border border-neutral-200 shadow-sm">

    <div class="flex items-center justify-between p-5 border-b">

        <div>

            <h2 class="text-lg font-semibold">

                Estado de Trabajos

            </h2>

            <div class="text-sm text-neutral-500 mt-1">

                {{ queue.length }} ejecución(es)

            </div>

        </div>

        <button
            @click="load"
            class="text-sm text-blue-600 hover:underline"
        >
            Actualizar
        </button>

    </div>

    <div class="p-5">

        <div
            v-if="loading"
            class="text-center text-neutral-500 py-10"
        >
            Cargando...
        </div>

        <template v-else>

            <div
                v-if="last"
                class="space-y-4"
            >

                <div class="flex justify-between">
                    <span>Trabajo</span>
                    <strong>{{ last.name }}</strong>
                </div>

                <div class="flex justify-between">
                    <span>Estado</span>

                    <span
                        class="px-3 py-1 rounded-full text-xs font-semibold"
                        :class="badge(last.status)"
                    >
                        {{ last.status }}
                    </span>

                </div>

                <div class="flex justify-between">
                    <span>Worker</span>
                    <strong>{{ last.worker || "-" }}</strong>
                </div>

                <div class="flex justify-between">
                    <span>Intentos</span>
                    <strong>{{ last.attempts }}</strong>
                </div>

                <div class="flex justify-between">
                    <span>Creado</span>
                    <strong>{{ last.created_at }}</strong>
                </div>

                <div class="flex justify-between">
                    <span>Inicio</span>
                    <strong>{{ last.started_at || "-" }}</strong>
                </div>

                <div class="flex justify-between">
                    <span>Fin</span>
                    <strong>{{ last.finished_at || "-" }}</strong>
                </div>

                <div
                    v-if="last.last_error"
                    class="rounded-lg border border-red-200 bg-red-50 p-3"
                >

                    <div class="font-semibold text-red-700">

                        Último error

                    </div>

                    <div class="text-red-600 text-sm mt-2">

                        {{ last.last_error }}

                    </div>

                </div>

            </div>

            <div
                v-else
                class="text-center text-neutral-500 py-10"
            >

                No existen trabajos en cola.

            </div>

        </template>

    </div>

</div>
</template>

<script setup>

import {
    ref,
    computed,
    onMounted,
    onUnmounted
} from "vue";

import {
    getQueue
} from "@/api/client";

const queue = ref([]);

const loading = ref(false);

let timer = null;

const last = computed(() => {

    if (!queue.value.length) {
        return null;
    }

    return queue.value[0];

});

async function load() {

    loading.value = true;

    try {

        const response = await getQueue();

        queue.value = response.data ?? [];

    } finally {

        loading.value = false;

    }

}

function badge(status) {

    switch (status) {

        case "Pending":
            return "bg-yellow-100 text-yellow-700";

        case "Running":
            return "bg-blue-100 text-blue-700";

        case "Completed":
            return "bg-green-100 text-green-700";

        case "Failed":
            return "bg-red-100 text-red-700";

        default:
            return "bg-neutral-100 text-neutral-700";

    }

}

onMounted(() => {

    load();

    timer = setInterval(load, 3000);

});

onUnmounted(() => {

    clearInterval(timer);

});

</script>