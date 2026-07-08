<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">

    <div class="flex justify-between items-center mb-5">

        <h2 class="text-lg font-semibold">

            Estado del Sistema

        </h2>

        <span
            class="text-xs px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold"
        >
            ONLINE
        </span>

    </div>

    <div class="space-y-4">

        <div class="flex justify-between">

            <span class="text-neutral-600">

                Scheduler

            </span>

            <span class="font-semibold text-green-600">

                ● Activo

            </span>

        </div>

        <div class="flex justify-between">

            <span class="text-neutral-600">

                Worker

            </span>

            <span class="font-semibold text-green-600">

                ● Activo

            </span>

        </div>

        <div class="flex justify-between">

            <span class="text-neutral-600">

                Hora actual

            </span>

            <span>

                {{ time }}

            </span>

        </div>

        <div class="flex justify-between">

            <span class="text-neutral-600">

                Uptime

            </span>

            <span>

                {{ uptime }}

            </span>

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

const time = ref("");

const uptime = ref("00:00:00");

let seconds = 0;

let timer = null;

function refresh(){

    time.value = new Date().toLocaleTimeString();

    seconds++;

    const h = String(Math.floor(seconds / 3600)).padStart(2,"0");

    const m = String(Math.floor((seconds % 3600) / 60)).padStart(2,"0");

    const s = String(seconds % 60).padStart(2,"0");

    uptime.value = `${h}:${m}:${s}`;

}

onMounted(()=>{

    refresh();

    timer = setInterval(refresh,1000);

});

onUnmounted(()=>{

    clearInterval(timer);

});

</script>