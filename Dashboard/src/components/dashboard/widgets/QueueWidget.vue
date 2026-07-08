<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm">

    <div class="flex items-center justify-between p-5 border-b">

        <div>

            <h2 class="text-lg font-semibold">

                Cola de trabajos

            </h2>

            <div class="text-sm text-neutral-500 mt-1">

                {{ queue.length }} registro(s)

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
            class="text-center text-neutral-500 py-8"
        >

            Cargando...

        </div>

        <div
            v-else-if="queue.length===0"
            class="text-center text-neutral-500 py-8"
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

                    <div class="text-xs text-neutral-500 mt-1">

                        Worker:
                        {{ item.worker ?? "-" }}

                    </div>

                </div>

                <div class="text-right">

                    <span
                        class="px-3 py-1 rounded-full text-xs font-semibold"
                        :class="badge(item.status)"
                    >

                        {{ item.status }}

                    </span>

                    <div
                        class="text-xs text-neutral-500 mt-2"
                    >

                        Intentos:
                        {{ item.attempts }}

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</template>

<script setup>

import {
    ref,
    onMounted,
    onUnmounted
} from "vue";

import {
    getQueue
} from "@/api/client";

const queue = ref([]);

const loading = ref(true);

let timer = null;

async function load(){

    try{

        loading.value = true;

        const response = await getQueue();

        queue.value = response.data ?? response;

    }
    catch(error){

        console.error(error);

    }
    finally{

        loading.value = false;

    }

}

function badge(status){

    switch(status){

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

onMounted(()=>{

    load();

    timer = setInterval(load,2000);

});

onUnmounted(()=>{

    if(timer){

        clearInterval(timer);

    }

});

</script>