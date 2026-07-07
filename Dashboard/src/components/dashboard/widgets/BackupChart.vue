<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">

    <div class="flex justify-between items-center mb-6">

        <h2 class="text-lg font-semibold">

            Actividad de Backups (7 días)

        </h2>

        <button
            @click="load"
            class="text-sm text-blue-600 hover:underline"
        >

            Actualizar

        </button>

    </div>

    <div style="height:320px">

        <Bar
            v-if="loaded"
            :data="chartData"
            :options="chartOptions"
        />

        <div
            v-else
            class="text-center py-24 text-neutral-500"
        >

            Cargando gráfico...

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

    Bar

} from "vue-chartjs";

import {

    Chart as ChartJS,

    Title,
    Tooltip,
    Legend,

    BarElement,

    CategoryScale,
    LinearScale

} from "chart.js";

import {

    getChart

} from "@/api/client";

ChartJS.register(

    Title,
    Tooltip,
    Legend,

    BarElement,

    CategoryScale,
    LinearScale

);

const loaded = ref(false);

const chartData = ref({

    labels:[],

    datasets:[]

});

const chartOptions={

    responsive:true,

    maintainAspectRatio:false,

    scales:{

        y:{

            beginAtZero:true,

            ticks:{

                precision:0

            }

        }

    }

};

async function load(){

    loaded.value=false;

    const json=await getChart();

    chartData.value={

        labels:json.data.map(x=>x.day),

        datasets:[

            {

                label:"Archivos subidos",

                data:json.data.map(x=>x.uploaded)

            }

        ]

    };

    loaded.value=true;

}

onMounted(load);

</script>