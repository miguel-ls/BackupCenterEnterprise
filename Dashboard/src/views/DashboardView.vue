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

    <div class="grid grid-cols-2 gap-6 mt-6">

        <div class="bg-white rounded-xl border shadow-sm p-6">

            <h2 class="text-xl font-bold mb-6">

                🖥 Infraestructura

            </h2>

            <div class="space-y-5">

                <div class="flex justify-between">

                    <span>Servidor</span>

                    <strong>{{ system.hostname }}</strong>

                </div>

                <div class="flex justify-between">

                    <span>Sistema</span>

                    <strong>{{ system.os }}</strong>

                </div>

                <div class="flex justify-between">

                    <span>PHP</span>

                    <strong>{{ system.php_version }}</strong>

                </div>

                <div class="flex justify-between">

                    <span>Uptime</span>

                    <strong>{{ system.uptime }}</strong>

                </div>

                <!-- CPU -->

                <div>

                    <div class="flex justify-between mb-1">

                        <span>CPU</span>

                        <strong>{{ system.cpu }} %</strong>

                    </div>

                    <div class="w-full bg-neutral-200 rounded-full h-3">

                        <div

                            class="h-3 rounded-full transition-all"

                            :style="{

                                width: system.cpu + '%',

                                background: cpuColor

                            }"

                        ></div>

                    </div>

                </div>

                <!-- RAM -->

                <div>

                    <div class="flex justify-between mb-1">

                        <span>RAM</span>

                        <strong>

                            {{ system.memory_used }} / {{ system.memory_total }} GB

                        </strong>

                    </div>

                    <div class="w-full bg-neutral-200 rounded-full h-3">

                        <div

                            class="h-3 rounded-full transition-all"

                            :style="{

                                width: ramPercent + '%',

                                background: ramColor

                            }"

                        ></div>

                    </div>

                </div>

                <!-- Disco -->

                <div>

                    <div class="flex justify-between mb-1">

                        <span>Disco</span>

                        <strong>

                            {{ system.disk_free }} / {{ system.disk_total }} GB

                        </strong>

                    </div>

                    <div class="w-full bg-neutral-200 rounded-full h-3">

                        <div

                            class="h-3 rounded-full transition-all"

                            :style="{

                                width: diskPercent + '%',

                                background: '#2563eb'

                            }"

                        ></div>

                    </div>

                </div>

            </div>

        </div>

        <SchedulerStatus/>

    </div>

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
    getVersion,
    getSystemInfo

} from "../api/client"

import { onMounted } from "vue"

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
        getVersion(),
        getSystemInfo()

    ])

    status.value = s.data

    statistics.value = st.data

    version.value = v.data

    system.value = si.data

}

onMounted(async () => {

    await load()

})

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