<template>

<aside class="w-72 bg-slate-900 text-white flex flex-col shadow-2xl">

    <!-- Logo -->

    <div class="border-b border-slate-700 p-6">

        <div class="flex items-center gap-3">

            <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center text-2xl">

                🛡

            </div>

            <div>

                <h1 class="text-xl font-bold">

                    Backup Center

                </h1>

                <div class="text-xs text-slate-400">

                    Enterprise Edition

                </div>

            </div>

        </div>

    </div>

    <!-- Estado -->

    <div class="border-b border-slate-700 p-5">

        <div class="flex justify-between items-center">

            <span class="text-sm text-slate-300">

                Estado

            </span>

            <span class="bg-green-600 px-3 py-1 rounded-full text-xs font-semibold">

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

            <span>📊</span>

            <span>Dashboard</span>

        </RouterLink>

        <RouterLink to="/jobs" class="menu">

            <span>💼</span>

            <span>Trabajos</span>

        </RouterLink>

        <RouterLink to="/connections" class="menu">

            <span>🌐</span>

            <span>Conexiones</span>

        </RouterLink>

        <RouterLink to="/queue" class="menu">

            <span>📋</span>

            <span>Cola</span>

        </RouterLink>

        <RouterLink to="/history" class="menu">

            <span>🕘</span>

            <span>Historial</span>

        </RouterLink>

        <RouterLink to="/logs" class="menu">

            <span>📄</span>

            <span>Logs</span>

        </RouterLink>

        <RouterLink to="/reports" class="menu">

            <span>📈</span>

            <span>Reportes</span>

            <span class="badge">

                NEW

            </span>

        </RouterLink>

        <div class="title mt-8">

            SEGURIDAD

        </div>

        <RouterLink to="/users" class="menu">

            <span>👤</span>

            <span>Usuarios</span>

        </RouterLink>

        <RouterLink to="/audit" class="menu">

            <span>🛡</span>

            <span>Auditoría</span>

        </RouterLink>

        <div class="title mt-8">

            SISTEMA

        </div>

        <RouterLink to="/settings" class="menu">

            <span>⚙️</span>

            <span>Configuración</span>

        </RouterLink>

        <RouterLink to="/about" class="menu">

            <span>ℹ️</span>

            <span>Acerca de</span>

        </RouterLink>

    </nav>

    <!-- Pie -->

    <div class="border-t border-slate-700 p-5">

        <div class="font-semibold">

            Backup Center Enterprise

        </div>

        <div class="text-xs text-slate-400 mt-1">

            BCE-003

        </div>

        <div class="mt-4">

            <div class="flex justify-between text-xs">

                <span>Versión</span>

                <strong>1.0.0</strong>

            </div>

            <div class="flex justify-between text-xs mt-2">

                <span>Estado</span>

                <strong class="text-green-400">

                    Operativo

                </strong>

            </div>

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

    display:flex;

    align-items:center;

    gap:12px;

    padding:14px 16px;

    margin-bottom:8px;

    border-radius:12px;

    transition:.2s;

    color:#e5e7eb;

}

.menu:hover{

    background:#1e293b;

    transform:translateX(5px);

}

.router-link-active{

    background:#2563eb;

    color:white;

    font-weight:700;

    box-shadow:0 0 15px rgba(37,99,235,.35);

}

.badge{

    margin-left:auto;

    background:#10b981;

    color:white;

    font-size:10px;

    padding:2px 8px;

    border-radius:9999px;

    font-weight:bold;

}

</style>