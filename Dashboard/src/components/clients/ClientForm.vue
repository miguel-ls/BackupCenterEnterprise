<template>

<div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
>

    <div class="w-full max-w-4xl rounded-xl bg-white shadow-2xl">

        <div class="flex items-center justify-between border-b px-6 py-4">

            <h2 class="text-xl font-bold">

                {{ form.id ? "Editar Cliente" : "Nuevo Cliente" }}

            </h2>

            <button
                class="text-2xl text-neutral-500 hover:text-red-600"
                @click="close"
            >

                ×

            </button>

        </div>

        <div class="grid grid-cols-2 gap-5 p-6">

            <div>

                <label class="block mb-1 font-semibold">

                    Código

                </label>

                <input
                    v-model="form.code"
                    class="w-full rounded-lg border p-2"
                >

            </div>

            <div>

                <label class="block mb-1 font-semibold">

                    RUC

                </label>

                <input
                    v-model="form.ruc"
                    class="w-full rounded-lg border p-2"
                >

            </div>

            <div class="col-span-2">

                <label class="block mb-1 font-semibold">

                    Razón Social

                </label>

                <input
                    v-model="form.business_name"
                    class="w-full rounded-lg border p-2"
                >

            </div>

            <div class="col-span-2">

                <label class="block mb-1 font-semibold">

                    Nombre Comercial

                </label>

                <input
                    v-model="form.trade_name"
                    class="w-full rounded-lg border p-2"
                >

            </div>

            <div>

                <label class="block mb-1 font-semibold">

                    Contacto

                </label>

                <input
                    v-model="form.contact_name"
                    class="w-full rounded-lg border p-2"
                >

            </div>

            <div>

                <label class="block mb-1 font-semibold">

                    Teléfono

                </label>

                <input
                    v-model="form.phone"
                    class="w-full rounded-lg border p-2"
                >

            </div>

            <div class="col-span-2">

                <label class="block mb-1 font-semibold">

                    Correo

                </label>

                <input
                    v-model="form.email"
                    type="email"
                    class="w-full rounded-lg border p-2"
                >

            </div>

            <div class="col-span-2">

                <label class="block mb-1 font-semibold">

                    Dirección

                </label>

                <input
                    v-model="form.address"
                    class="w-full rounded-lg border p-2"
                >

            </div>

            <div>

                <label class="block mb-1 font-semibold">

                    Estado

                </label>

                <select
                    v-model="form.status"
                    class="w-full rounded-lg border p-2"
                >

                    <option :value="1">Activo</option>
                    <option :value="0">Inactivo</option>

                </select>

            </div>

            <div class="col-span-2">

                <label class="block mb-1 font-semibold">

                    Observaciones

                </label>

                <textarea
                    v-model="form.notes"
                    rows="4"
                    class="w-full rounded-lg border p-2"
                ></textarea>

            </div>

        </div>

        <div class="flex justify-end gap-3 border-t px-6 py-4">

            <button
                class="rounded-lg border px-5 py-2"
                @click="close"
            >

                Cancelar

            </button>

            <button
                class="rounded-lg bg-blue-600 px-5 py-2 text-white hover:bg-blue-700"
                @click="save"
            >

                Guardar

            </button>

        </div>

    </div>

</div>

</template>

<script setup>

import {

    reactive,
    watch

} from "vue"

const props = defineProps({

    show: Boolean,

    client: {

        type: Object,

        default: null

    }

})

const emit = defineEmits([

    "close",
    "save"

])

const emptyForm = {

    id: null,

    code: "",

    business_name: "",

    trade_name: "",

    ruc: "",

    contact_name: "",

    email: "",

    phone: "",

    address: "",

    status: 1,

    notes: ""

}

const form = reactive({

    ...emptyForm

})

watch(

    () => props.client,

    (client) => {

        if (client) {

            Object.assign(form, emptyForm, client)

        }
        else {

            Object.assign(form, emptyForm)

        }

    },

    {

        immediate: true

    }

)

function close() {

    emit("close")

}

function save() {

    emit("save", {

        ...form

    })

}

</script>