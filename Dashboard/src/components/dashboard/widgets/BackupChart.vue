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

        <div class="flex items-center gap-3">

            <select
                v-model="selectedClient"
                @change="onUpdate"
                class="border rounded-md px-3 py-2 text-sm"
            >

                <option :value="0">Todos los clientes</option>

                <option
                    v-for="client in clients"
                    :key="client.id"
                    :value="client.id"
                >
                    {{ client.business_name }}
                </option>

            </select>

            <button
                @click="onUpdate"
                class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition"
            >

                Actualizar

            </button>

        </div>

    </div>

    <div class="p-6">

        <div style="height:360px">

            <Line
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

    Line

} from "vue-chartjs"

import {

    Chart as ChartJS,

    Title,
    Tooltip,
    Legend,

    LineElement,
    PointElement,

    CategoryScale,
    LinearScale

} from "chart.js"

import {

    getChart,
    getClients,
    getHistory

} from "@/api/client"

ChartJS.register(

    Title,
    Tooltip,
    Legend,

    LineElement,
    PointElement,

    CategoryScale,
    LinearScale

)

const loaded = ref(false)
const clients = ref([])
const selectedClient = ref(0)

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

async function __loadBackupChartData() {

    loaded.value = false

    if (selectedClient.value === 0) {

        const response = await getChart()

        chartData.value = {

            labels: response.data.map(item => item.day),

            datasets:[

                {

                    label:"Archivos subidos",

                    data: response.data.map(item => item.uploaded),

                    borderColor: "#2563EB",
                    backgroundColor: "rgba(37,99,235,0.08)",
                    fill: true,
                    tension: 0.25,
                    pointRadius: 3

                }

            ]

        }

    } else {

        // get history for client and aggregate per day for last 7 days
        const resp = await getHistory(1,100,selectedClient.value)
        const rows = Array.isArray(resp.data) ? resp.data : []

        const labels = []
        const map = {}
        for (let i = 6; i >= 0; i--) {
            const d = new Date()
            d.setDate(d.getDate() - i)
            const day = d.toISOString().slice(0,10)
            labels.push(day)
            map[day] = 0
        }

        rows.forEach(item => {
            const day = (item.executed_at || '').slice(0,10)
            if (day in map) {
                map[day] += Number(item.files_uploaded) || 0
            }
        })

        chartData.value = {
            labels: labels,
            datasets: [
                {
                    label: "Archivos subidos",
                    data: labels.map(l => map[l] || 0),
                    borderColor: "#2563EB",
                    backgroundColor: "rgba(37,99,235,0.08)",
                    fill: true,
                    tension: 0.25,
                    pointRadius: 3
                }
            ]
        }

    }

    loaded.value = true

}

function onUpdate() {
    return __loadBackupChartData()
}

onMounted(()=>{

    onUpdate()

    // load clients for selector
    async function loadClients(){
        try{
            const r = await getClients()
            clients.value = Array.isArray(r.data) ? r.data : []
        } catch(e){
            clients.value = []
        }
    }

    loadClients()

    timer = setInterval(onUpdate,20000)

})

onUnmounted(()=>{

    clearInterval(timer)

})

</script>