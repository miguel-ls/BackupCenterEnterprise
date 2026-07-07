<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm">

    <div class="flex items-center justify-between p-5 border-b">

        <h2 class="text-lg font-semibold">

            Cola de trabajos

        </h2>

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
            class="text-center text-neutral-500 py-6"
        >
            Cargando...
        </div>

        <div
            v-else-if="queue.length === 0"
            class="text-center text-neutral-500 py-6"
        >
            No existen trabajos en cola.
        </div>

        <div
            v-else
            class="space-y-3"
        >

            <div
                v-for="item in queue.slice(0,5)"
                :key="item.id"
                class="flex justify-between items-center border rounded-lg p-3"
            >

                <div>

                    <div class="font-semibold">

                        {{ item.name }}

                    </div>

                    <div class="text-sm text-neutral-500">

                        {{ item.status }}

                    </div>

                </div>

                <div>

                    <span
                        class="text-sm px-3 py-1 rounded-full bg-blue-100 text-blue-700"
                    >

                        {{ item.status }}

                    </span>

                </div>

            </div>

        </div>

    </div>

</div>

</template>

<script setup>

import {
    ref,
    onMounted
} from "vue";

import {
    getQueue
} from "@/api/client";

const queue = ref([]);
const loading = ref(true);

async function load(){

    loading.value = true;

    try{

        const response = await getQueue();

        queue.value = response.data ?? response;

    }finally{

        loading.value = false;

    }

}

onMounted(load);

</script>