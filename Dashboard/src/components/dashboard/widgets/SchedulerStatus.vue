<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">

    <h2 class="text-lg font-semibold mb-4">

        Estado del Scheduler

    </h2>

    <div class="flex items-center justify-between">

        <div>

            <div class="text-sm text-neutral-500">
                Servicio
            </div>

            <div
                class="font-semibold text-green-600 text-lg"
            >
                ● Activo
            </div>

        </div>

        <div class="text-right">

            <div class="text-sm text-neutral-500">
                Última actualización
            </div>

            <div>
                {{ time }}
            </div>

        </div>

    </div>

</div>

</template>

<script setup>

import { ref,onMounted,onUnmounted } from 'vue'

const time = ref('')

let timer

function refresh(){

    time.value = new Date().toLocaleTimeString()

}

onMounted(()=>{

    refresh()

    timer = setInterval(refresh,1000)

})

onUnmounted(()=>{

    clearInterval(timer)

})

</script>