<template>

<header class="h-16 bg-white border-b border-neutral-200 flex items-center justify-between px-8 relative">

    <div>

        <h2 class="text-xl font-semibold">

            Backup Center Enterprise

        </h2>

    </div>

    <div class="flex items-center gap-6">

        <!-- Usuario -->

        <div class="flex items-center gap-3">

            <div
                class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold"
            >
                {{ initial }}
            </div>

            <div>

                <div class="font-semibold">

                    {{ user.fullname }}

                </div>

                <div class="text-xs text-neutral-500">

                    {{ user.role }}

                </div>

            </div>

            <button
                @click="logout"
                class="ml-2 text-red-600 hover:underline text-sm"
            >

                Cerrar sesión

            </button>

        </div>

        <!-- Notificaciones -->

        <div class="relative">

            <button
                @click="show = !show"
                class="relative text-2xl hover:scale-110 transition"
            >

                🔔

                <span
                    v-if="unread>0"
                    class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full min-w-[20px] h-5 px-1 flex items-center justify-center text-xs font-bold"
                >

                    {{ unread }}

                </span>

            </button>

            <div
                v-if="show"
                class="absolute right-0 mt-3 w-96 bg-white border rounded-xl shadow-xl z-50"
            >

                <div class="flex justify-between items-center p-4 border-b">

                    <h3 class="font-semibold">

                        Notificaciones

                    </h3>

                    <span class="text-sm text-neutral-500">

                        {{ notifications.length }}

                    </span>

                </div>

                <div class="max-h-96 overflow-y-auto">

                    <div
                        v-for="item in notifications.slice(0,5)"
                        :key="item.id"
                        class="p-4 border-b hover:bg-neutral-50"
                    >

                        <div class="flex justify-between">

                            <span
                                class="font-semibold"
                                :class="color(item.level)"
                            >

                                {{ item.title }}

                            </span>

                            <span class="text-xs text-neutral-500">

                                {{ item.created_at }}

                            </span>

                        </div>

                        <div class="text-sm text-neutral-600 mt-2">

                            {{ item.message }}

                        </div>

                    </div>

                    <div
                        v-if="notifications.length===0"
                        class="text-center p-8 text-neutral-500"
                    >

                        No existen notificaciones.

                    </div>

                </div>

                <div class="flex justify-between p-4 border-t">

                    <button
                        class="text-blue-600 hover:underline"
                        @click="markAll"
                    >

                        Marcar leídas

                    </button>

                    <button
                        class="text-red-600 hover:underline"
                        @click="clearAll"
                    >

                        Limpiar

                    </button>

                </div>

            </div>

        </div>

        <!-- Estado -->

        <div class="flex items-center gap-3">

            <div class="w-3 h-3 rounded-full bg-green-500"></div>

            <span class="text-sm text-neutral-600">

                Servicio activo

            </span>

        </div>

    </div>

</header>

</template>

<script setup>

import {

    ref,
    computed,
    onMounted,
    onUnmounted

} from "vue";

import { useRouter } from "vue-router";

import {

    getNotifications,
    markNotificationsAsRead,
    clearNotifications

} from "@/api/client";

const router = useRouter();

const unread = ref(0);

const notifications = ref([]);

const show = ref(false);

const user = JSON.parse(
    localStorage.getItem("user") ?? "{}"
);

const initial = computed(()=>

    (user.fullname ?? "?")
        .substring(0,1)
        .toUpperCase()

);

let timer = null;

function logout(){

    localStorage.removeItem("user");

    router.push("/login");

}

function color(level){

    switch(level){

        case "ERROR":

            return "text-red-600";

        case "WARNING":

            return "text-yellow-600";

        default:

            return "text-green-600";

    }

}

async function load(){

    try{

        const response = await getNotifications();

        notifications.value = response.data;

        unread.value = response.unread;

    }
    catch(e){

        console.error(e);

    }

}

async function markAll(){

    await markNotificationsAsRead();

    await load();

}

async function clearAll(){

    if(!confirm("¿Eliminar todas las notificaciones?")){

        return;

    }

    await clearNotifications();

    await load();

}

onMounted(()=>{

    load();

    timer = setInterval(load,5000);

});

onUnmounted(()=>{

    clearInterval(timer);

});

</script>