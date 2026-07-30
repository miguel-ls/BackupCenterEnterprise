<template>

<MainLayout>

<div class="flex justify-between items-center mb-8">

<h1 class="text-3xl font-bold">

Auditoría

</h1>

<div class="flex gap-3">

<input
v-model="search"
placeholder="Buscar..."
class="border rounded-lg px-4 py-2 w-72"
/>

<button
@click="clearAudit"
class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg"
>

Limpiar

</button>

</div>

</div>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">

<table class="w-full">

<thead class="bg-neutral-100">

<tr>

<th class="p-3 text-left">Fecha</th>
<th class="p-3 text-left">Usuario</th>
<th class="p-3 text-left">Módulo</th>
<th class="p-3 text-left">Acción</th>
<th class="p-3 text-left">Descripción</th>
<th class="p-3 text-left">IP</th>
<th class="p-3 text-left">Host</th>
<th class="p-3 text-center">Estado</th>

</tr>

</thead>

<tbody>

<tr
v-for="item in filtered"
:key="item.id"
class="border-t hover:bg-neutral-50"
>

<td class="p-3">

{{item.created_at}}

</td>

<td class="p-3">

{{item.username}}

</td>

<td class="p-3">

<span class="font-semibold">

{{item.module}}

</span>

</td>

<td class="p-3">

{{item.action}}

</td>

<td class="p-3">

{{item.description}}

</td>

<td class="p-3">

{{item.ip}}

</td>

<td class="p-3">

{{item.hostname}}

</td>

<td class="p-3 text-center">

<span
class="px-3 py-1 rounded-full text-xs font-semibold"
:class="item.success?'bg-green-100 text-green-700':'bg-red-100 text-red-700'"
>

{{item.success?'OK':'ERROR'}}

</span>

</td>

</tr>

<tr v-if="filtered.length==0">

<td
colspan="8"
class="text-center p-8 text-neutral-500"
>

No existen registros.

</td>

</tr>

</tbody>

</table>

</div>

<div class="flex justify-between mt-6">

<button
@click="prev"
:disabled="page==1"
class="border px-5 py-2 rounded-lg"
>

Anterior

</button>

<div>

Página {{page}}

</div>

<button
@click="next"
class="border px-5 py-2 rounded-lg"
>

Siguiente

</button>

</div>

</MainLayout>

</template>

<script setup>

import {ref,computed,onMounted} from "vue"

import MainLayout from "../components/layout/MainLayout.vue"

const API = import.meta.env.VITE_API_URL;

const audit=ref([])

const page=ref(1)

const search=ref("")

async function load(){

const r=await fetch(

`${API}/audit.php?page=${page.value}&limit=50`

)

const j=await r.json()

audit.value=j.data.items??[]

}

const filtered=computed(()=>{

return audit.value.filter(x=>{

const s=search.value.toLowerCase()

return(

(x.username??"").toLowerCase().includes(s)||

(x.module??"").toLowerCase().includes(s)||

(x.action??"").toLowerCase().includes(s)||

(x.description??"").toLowerCase().includes(s)

)

})

})

function next(){

page.value++

load()

}

function prev(){

if(page.value>1){

page.value--

load()

}

}

async function clearAudit(){

if(!confirm("¿Eliminar auditoría?")){

return

}

await fetch(

`${API}/audit.php`,


{

method:"DELETE"

}

)

load()

}

onMounted(load)

</script>