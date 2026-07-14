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

<!-- Sistema -->

<div class="grid grid-cols-2 gap-6 mt-6">

<div class="bg-white rounded-xl border shadow-sm p-6">

<h2 class="text-xl font-semibold mb-4">

Información del Servidor

</h2>

<div class="space-y-3">

<div class="flex justify-between">
<span>Servidor</span>
<strong>{{system.hostname}}</strong>
</div>

<div class="flex justify-between">
<span>Sistema</span>
<strong>{{system.os}}</strong>
</div>

<div class="flex justify-between">
<span>PHP</span>
<strong>{{system.php_version}}</strong>
</div>

<div class="flex justify-between">
<span>Hora</span>
<strong>{{system.time}}</strong>
</div>

<div class="flex justify-between">
<span>RAM usada</span>
<strong>{{system.memory_usage}} MB</strong>
</div>

<div class="flex justify-between">
<span>Pico RAM</span>
<strong>{{system.memory_peak}} MB</strong>
</div>

<div class="flex justify-between">
<span>Disco Libre</span>
<strong>{{system.disk_free}} GB</strong>
</div>

<div class="flex justify-between">
<span>Disco Total</span>
<strong>{{system.disk_total}} GB</strong>
</div>

</div>

</div>

<div>

<SchedulerStatus/>

</div>

</div>

<!-- Últimas ejecuciones -->

<div class="grid grid-cols-3 gap-6 mt-6">

<div class="col-span-2">

<LastExecutions/>

</div>

<div>

<QueueWidget/>

</div>

</div>

<div class="mt-6">

<BackupChart/>

</div>

</MainLayout>

</template>

<script setup>

import { ref } from "vue";

import MainLayout from "../components/layout/MainLayout.vue";
import StatCard from "../components/cards/StatCard.vue";

import SchedulerStatus from "../components/dashboard/widgets/SchedulerStatus.vue";
import LastExecutions from "../components/dashboard/LastExecutions.vue";
import QueueWidget from "../components/dashboard/widgets/QueueWidget.vue";
import BackupChart from "../components/dashboard/widgets/BackupChart.vue";

import {
    getStatus,
    getStatistics,
    getVersion,
    getSystemInfo
} from "../api/client";

import { useAutoRefresh } from "../composables/useAutoRefresh";

const status = ref({});
const statistics = ref({});
const version = ref({});
const system = ref({});

async function load() {

    try {

        const [
            statusResponse,
            statisticsResponse,
            versionResponse,
            systemResponse
        ] = await Promise.all([

            getStatus(),
            getStatistics(),
            getVersion(),
            getSystemInfo()

        ]);

        status.value = statusResponse.data;

        statistics.value = statisticsResponse.data;

        version.value = versionResponse.data;

        system.value = systemResponse.data;

    } catch (e) {

        console.error(e);

    }

}

useAutoRefresh(load,5000);

</script>