<template>

<aside class="w-56 bg-neutral-900 text-white flex flex-col">

    <div class="h-16 flex items-center px-6 border-b border-neutral-800">

        <h1 class="text-2xl font-bold">

            Backup Center

        </h1>

    </div>

    <nav class="flex-1 p-4 space-y-2">

        <RouterLink
            to="/"
            class="block px-4 py-3 rounded-lg hover:bg-neutral-800"
        >
            Dashboard
        </RouterLink>

        <RouterLink
            to="/jobs"
            class="block px-4 py-3 rounded-lg hover:bg-neutral-800"
        >
            Trabajos
        </RouterLink>

        <RouterLink
            to="/connections"
            class="block px-4 py-3 rounded-lg hover:bg-neutral-800"
        >
            Conexiones
        </RouterLink>

<RouterLink
    to="/queue"
    class="block px-4 py-3 rounded-lg hover:bg-neutral-800"
>
    Cola
</RouterLink>
        
        <RouterLink
            to="/history"
            class="block px-4 py-3 rounded-lg hover:bg-neutral-800"
        >
            Historial
        </RouterLink>

        <RouterLink
            to="/logs"
            class="block px-4 py-3 rounded-lg hover:bg-neutral-800"
        >
            Logs
        </RouterLink>

        <RouterLink
            to="/settings"
            class="block px-4 py-3 rounded-lg hover:bg-neutral-800"
        >
            Configuración
        </RouterLink>

        <RouterLink
            to="/about"
            class="block px-4 py-3 rounded-lg hover:bg-neutral-800"
        >
            Acerca de
        </RouterLink>

    </nav>

</aside>

</template>

<script setup>

import { RouterLink } from 'vue-router'

</script>