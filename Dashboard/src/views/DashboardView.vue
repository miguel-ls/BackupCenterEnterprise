<template>

<MainLayout>

    <h1 class="text-3xl font-bold mb-8">

        Dashboard

    </h1>

    <!-- KPIs -->

    <div class="grid grid-cols-4 gap-6">

        <StatCard
            title="Trabajos"
            :value="status.jobs ?? 0"
        />

        <StatCard
            title="Conexiones"
            :value="status.connections ?? 0"
        />

        <StatCard
            title="En Cola"
            :value="status.queue ?? 0"
        />

        <StatCard
            title="Ejecutando"
            :value="status.running ?? 0"
        />

    </div>

    <!-- Estadísticas -->

    <div class="grid grid-cols-4 gap-6 mt-6">

        <StatCard
            title="Subidos Hoy"
            :value="statistics.uploaded_today ?? 0"
        />

        <StatCard
            title="Ejecuciones Hoy"
            :value="statistics.executions_today ?? 0"
        />

        <StatCard
            title="Errores Hoy"
            :value="statistics.errors_today ?? 0"
        />

        <StatCard
            title="Total Archivos"
            :value="statistics.total_uploaded ?? 0"
        />

    </div>

    <!-- Información -->

    <div class="grid grid-cols-2 gap-6 mt-8">

        <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">

            <h2 class="font-bold text-xl mb-4">

                Información

            </h2>

            <div class="space-y-3">

                <div class="flex justify-between">
                    <span>Versión</span>
                    <span>{{ version.version }}</span>
                </div>

                <div class="flex justify-between">
                    <span>PHP</span>
                    <span>{{ version.php }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Servidor</span>
                    <span>{{ version.time }}</span>
                </div>

            </div>

        </div>

        <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">

            <h2 class="font-bold text-xl mb-4">

                Estado General

            </h2>

            <div class="space-y-3">

                <div class="flex justify-between">
                    <span>Trabajos</span>
                    <span>{{ status.jobs }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Conexiones</span>
                    <span>{{ status.connections }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Cola</span>
                    <span>{{ status.queue }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Running</span>
                    <span>{{ status.running }}</span>
                </div>

            </div>

        </div>

    </div>

    <!-- Widgets superiores -->

    <div class="grid grid-cols-3 gap-6 mt-8">

        <div class="col-span-2">

            <LastExecutions />

        </div>

        <div>

            <SchedulerStatus />

        </div>

    </div>

    <!-- Gráfico + Cola -->

    <div class="grid grid-cols-3 gap-6 mt-6">

        <div class="col-span-2">

            <BackupChart />

        </div>

        <div>

            <QueueWidget />

        </div>

    </div>

</MainLayout>

</template>

<script setup>

import { onMounted, ref } from "vue";

import MainLayout from "../components/layout/MainLayout.vue";
import StatCard from "../components/cards/StatCard.vue";

import LastExecutions from "../components/dashboard/LastExecutions.vue";

import SchedulerStatus from "../components/dashboard/widgets/SchedulerStatus.vue";
import QueueWidget from "../components/dashboard/widgets/QueueWidget.vue";
import BackupChart from "../components/dashboard/widgets/BackupChart.vue";

import {

    getStatus,
    getStatistics,
    getVersion

} from "../api/client";

const status = ref({});
const statistics = ref({});
const version = ref({});

async function load(){

    const s = await getStatus();
    const st = await getStatistics();
    const v = await getVersion();

    status.value = s.data;
    statistics.value = st.data;
    version.value = v;

}

onMounted(()=>{

    load();

    setInterval(load,5000);

});

</script>