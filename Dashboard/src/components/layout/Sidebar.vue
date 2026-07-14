<template>

<aside class="w-72 bg-slate-900 text-white flex flex-col shadow-2xl">

    <!-- Logo -->

    <div class="border-b border-slate-700 p-6">

        <h1 class="text-2xl font-bold tracking-wide">

            Backup Center

        </h1>

        <p class="text-sm text-slate-400 mt-1">

            Enterprise Edition

        </p>

    </div>

    <!-- Estado -->

    <div class="px-5 py-4 border-b border-slate-700">

        <div class="flex items-center justify-between">

            <span class="text-sm text-slate-300">

                Estado

            </span>

            <span
                class="bg-green-600 px-2 py-1 rounded-full text-xs font-semibold"
            >
                ONLINE
            </span>

        </div>

    </div>

    <!-- Menú -->

    <nav class="flex-1 px-4 py-5 overflow-y-auto">

        <div class="title">

            GENERAL

        </div>

        <RouterLink to="/" class="menu">
            📊 Dashboard
        </RouterLink>

        <RouterLink to="/jobs" class="menu">
            💼 Trabajos
        </RouterLink>

        <RouterLink to="/connections" class="menu">
            🌐 Conexiones
        </RouterLink>

        <RouterLink to="/queue" class="menu">
            📋 Cola
        </RouterLink>

        <RouterLink to="/history" class="menu">
            🕘 Historial
        </RouterLink>

        <RouterLink to="/logs" class="menu">
            📄 Logs
        </RouterLink>

        <div class="title mt-8">

            SEGURIDAD

        </div>

        <RouterLink to="/users" class="menu">
            👤 Usuarios
        </RouterLink>

        <RouterLink to="/audit" class="menu">
            🛡 Auditoría
        </RouterLink>

        <div class="title mt-8">

            SISTEMA

        </div>

        <RouterLink to="/settings" class="menu">
            ⚙️ Configuración
        </RouterLink>

        <RouterLink to="/about" class="menu">
            ℹ️ Acerca de
        </RouterLink>

    </nav>

    <!-- Pie -->

    <div
        class="border-t border-slate-700 p-5 text-xs text-slate-400"
    >

        <div class="font-semibold text-white">

            Backup Center Enterprise

        </div>

        <div class="mt-1">

            Versión 1.0.0

        </div>

        <div class="mt-3 text-green-400">

            ● Sistema operativo

        </div>

    </div>

</aside>

</template>

<script setup>

import { RouterLink } from "vue-router"

</script>

<style scoped>

.title{

    font-size:11px;

    color:#94a3b8;

    font-weight:700;

    letter-spacing:2px;

    margin-bottom:12px;

    padding-left:12px;

}

.menu{

    display:block;

    padding:14px 16px;

    margin-bottom:8px;

    border-radius:12px;

    transition:all .20s;

    color:#e5e7eb;

}

.menu:hover{

    background:#1e293b;

    transform:translateX(4px);

}

.router-link-active{

    background:#2563eb;

    color:white;

    font-weight:700;

    box-shadow:0 0 15px rgba(37,99,235,.35);

}

</style>