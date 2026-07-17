<template>

<MainLayout>

    <h1 class="text-3xl font-bold mb-8">
        Dashboard
    </h1>

    <!-- KPI -->

    <div class="grid grid-cols-4 gap-6">

        <StatCard title="Trabajos" :value="status.jobs ?? 0"/>

        <StatCard title="Conexiones" :value="status.connections ?? 0"/>

        <StatCard title="En Cola" :value="status.queue ?? 0"/>

        <StatCard title="Ejecutando" :value="status.running ?? 0"/>

    </div>

    <!-- Estadísticas -->

    <div class="grid grid-cols-4 gap-6 mt-6">

        <StatCard title="Subidos Hoy" :value="statistics.uploaded_today ?? 0"/>

        <StatCard title="Ejecuciones Hoy" :value="statistics.executions_today ?? 0"/>

        <StatCard title="Errores Hoy" :value="statistics.errors_today ?? 0"/>

        <StatCard title="Total Archivos" :value="statistics.total_uploaded ?? 0"/>

    </div>

    <!-- Servidor + Monitor -->



    <!-- Ejecuciones -->

    <div class="grid grid-cols-3 gap-6 mt-6">

        <div class="col-span-2">

            <LastExecutions/>

        </div>

        <QueueWidget/>

    </div>

    <!-- Gráfico -->

<div class="grid grid-cols-2 gap-6 mt-6">

    <OperationsCenter/>

    <BackupChart/>

</div>

</MainLayout>

</template>

<script setup>

import { ref, computed } from "vue"

import MainLayout from "../components/layout/MainLayout.vue"
import StatCard from "../components/cards/StatCard.vue"

import SchedulerStatus from "../components/dashboard/widgets/SchedulerStatus.vue"
import LastExecutions from "../components/dashboard/LastExecutions.vue"
import QueueWidget from "../components/dashboard/widgets/QueueWidget.vue"
import BackupChart from "../components/dashboard/widgets/BackupChart.vue"

import {

    getStatus,
    getStatistics,
    getVersion

} from "../api/client"

import { useAutoRefresh } from "../composables/useAutoRefresh"

import OperationsCenter from "../components/dashboard/widgets/OperationsCenter.vue"

const status = ref({})
const statistics = ref({})
const version = ref({})
const system = ref({})

async function load(){

    const [

        s,
        st,
        v,
        si

    ] = await Promise.all([

        getStatus(),
        getStatistics(),
        getVersion()

    ])

    status.value = s.data

    statistics.value = st.data

    version.value = v.data



}

useAutoRefresh(load,20000)

const ramPercent = computed(()=>{

    if(!system.value.memory_total) return 0

    return Math.round(

        system.value.memory_used /

        system.value.memory_total *100

    )

})

const diskPercent = computed(()=>{

    if(!system.value.disk_total) return 0

    return Math.round(

        system.value.disk_free /

        system.value.disk_total *100

    )

})

const cpuColor = computed(()=>{

    if(system.value.cpu<60) return "#22c55e"

    if(system.value.cpu<85) return "#f59e0b"

    return "#dc2626"

})

const ramColor = computed(()=>{

    if(ramPercent.value<60) return "#22c55e"

    if(ramPercent.value<85) return "#f59e0b"

    return "#dc2626"

})

</script>