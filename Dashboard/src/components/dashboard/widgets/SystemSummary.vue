<template>

<div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">

    <div class="flex items-center justify-between mb-6">

        <h2 class="text-xl font-bold">

            Resumen Ejecutivo

        </h2>

        <span
            class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold"
        >
            ONLINE
        </span>

    </div>

    <div class="space-y-4">

        <div class="flex justify-between">

            <span>Versión</span>

            <strong>{{ version.version }}</strong>

        </div>

        <div class="flex justify-between">

            <span>PHP</span>

            <strong>{{ version.php }}</strong>

        </div>

        <div class="flex justify-between">

            <span>Trabajos</span>

            <strong>{{ status.jobs }}</strong>

        </div>

        <div class="flex justify-between">

            <span>Conexiones</span>

            <strong>{{ status.connections }}</strong>

        </div>

        <div class="flex justify-between">

            <span>En Cola</span>

            <strong>{{ status.queue }}</strong>

        </div>

        <div class="flex justify-between">

            <span>Ejecutando</span>

            <strong>{{ status.running }}</strong>

        </div>

        <hr>

        <div class="flex justify-between">

            <span>Subidos Hoy</span>

            <strong>{{ statistics.uploaded_today }}</strong>

        </div>

        <div class="flex justify-between">

            <span>Ejecuciones Hoy</span>

            <strong>{{ statistics.executions_today }}</strong>

        </div>

        <div class="flex justify-between">

            <span>Total Archivos</span>

            <strong>{{ statistics.total_uploaded }}</strong>

        </div>

        <div class="flex justify-between">

            <span>Servidor</span>

            <strong>{{ version.time }}</strong>

        </div>

    </div>

</div>

</template>

<script setup>

defineProps({

    version: Object,

    status: Object,

    statistics: Object

});

</script>