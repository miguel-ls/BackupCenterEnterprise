<template>

<div class="min-h-screen bg-slate-100 flex items-center justify-center">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-10">

        <h1 class="text-3xl font-bold text-center mb-2">

            Backup Center Enterprise

        </h1>

        <p class="text-center text-neutral-500 mb-8">

            Iniciar sesión

        </p>

        <form
            @submit.prevent="login"
            class="space-y-5"
        >

            <div>

                <label class="block mb-2">

                    Usuario

                </label>

                <input
                    v-model="username"
                    class="w-full border rounded-lg p-3"
                    autocomplete="username"
                >

            </div>

            <div>

                <label class="block mb-2">

                    Contraseña

                </label>

                <input
                    v-model="password"
                    type="password"
                    class="w-full border rounded-lg p-3"
                    autocomplete="current-password"
                >

            </div>

            <div
                v-if="error"
                class="bg-red-100 border border-red-300 text-red-700 rounded-lg p-3"
            >

                {{ error }}

            </div>

            <button
                class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-3 font-semibold"
            >

                Iniciar sesión

            </button>

        </form>

    </div>

</div>

</template>

<script setup>

import { ref } from "vue";

import { useRouter } from "vue-router";

const router = useRouter();

const username = ref("");

const password = ref("");

const error = ref("");

async function login(){

    error.value="";

    const response = await fetch(
        "http://localhost:8000/api/login.php",
        {
            method:"POST",

            headers:{
                "Content-Type":"application/json"
            },

            body:JSON.stringify({

                username:username.value,

                password:password.value

            })
        }
    );

    const json = await response.json();

    if(!json.success){

        error.value=json.message;

        return;

    }

    localStorage.setItem(

        "user",

        JSON.stringify(json.data)

    );

    router.push("/");

}

</script>