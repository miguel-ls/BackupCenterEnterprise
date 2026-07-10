<template>

<aside class="w-64 bg-neutral-900 text-white flex flex-col">

    <div class="h-16 flex items-center justify-center border-b border-neutral-800">

        <h1 class="text-xl font-bold">

            Backup Center

        </h1>

    </div>

    <nav class="flex-1 p-4">

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

        <div class="title mt-6">

            SEGURIDAD

        </div>

        <RouterLink to="/users" class="menu">
            👤 Usuarios
        </RouterLink>

        <RouterLink to="/audit" class="menu">
            🛡 Auditoría
        </RouterLink>

        <div class="title mt-6">

            SISTEMA

        </div>

        <RouterLink to="/settings" class="menu">
            ⚙️ Configuración
        </RouterLink>

        <RouterLink to="/about" class="menu">
            ℹ️ Acerca de
        </RouterLink>

    </nav>

</aside>

</template>

<script setup>

import { RouterLink } from "vue-router"

</script>

<style scoped>

.title{

    font-size:11px;

    color:#9ca3af;

    font-weight:bold;

    letter-spacing:1px;

    margin-bottom:10px;

    padding-left:10px;

}

.menu{

    display:block;

    padding:12px 14px;

    margin-bottom:6px;

    border-radius:10px;

    transition:.2s;

}

.menu:hover{

    background:#262626;

}

.router-link-active{

    background:#2563eb;

    color:white;

    font-weight:600;

}

</style>