<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm">

    <div class="flex items-center justify-between px-6 py-5 border-b">

        <div>

            <h2 class="text-xl font-bold">

                Actividad de Backups

            </h2>

            <div class="text-sm text-neutral-500 mt-1">

                Últimos 7 días

            </div>

        </div>

        <button
            @click="load"
            class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition"
        >

            Actualizar

        </button>

    </div>

    <div class="p-6">

        <div style="height:360px">

            <Bar
                v-if="loaded"
                :data="chartData"
                :options="chartOptions"
            />

            <div
                v-else
                class="flex items-center justify-center h-full text-neutral-500"
            >

                Cargando gráfico...

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

} from "vue"

import {

    Bar

} from "vue-chartjs"

import {

    Chart as ChartJS,

    Title,
    Tooltip,
    Legend,

    BarElement,

    CategoryScale,
    LinearScale

} from "chart.js"

import {

    getChart

} from "@/api/client"

ChartJS.register(

    Title,
    Tooltip,
    Legend,

    BarElement,

    CategoryScale,
    LinearScale

)

const loaded = ref(false)

const chartData = ref({

    labels:[],

    datasets:[]

})

const chartOptions = {

    responsive:true,

    maintainAspectRatio:false,

    interaction:{

        intersect:false,

        mode:"index"

    },

    plugins:{

        legend:{

            display:true,

            position:"top"

        },

        title:{

            display:false

        },

        tooltip:{

            enabled:true

        }

    },

    scales:{

        y:{

            beginAtZero:true,

            grid:{

                color:"#E5E7EB"

            },

            ticks:{

                precision:0

            }

        },

        x:{

            grid:{

                display:false

            }

        }

    }

}

let timer = null

async function load(){

    loaded.value = false

    const response = await getChart()

    chartData.value = {

        labels: response.data.map(item => item.day),

        datasets:[

            {

                label:"Archivos subidos",

                data: response.data.map(item => item.uploaded),

                backgroundColor:"#2563EB",

                borderRadius:8,

                borderSkipped:false

            }

        ]

    }

    loaded.value = true

}

onMounted(()=>{

    load()

    timer = setInterval(load,20000)

})

onUnmounted(()=>{

    clearInterval(timer)

})

</script>