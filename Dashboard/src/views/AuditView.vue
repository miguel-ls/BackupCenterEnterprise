<template>

<MainLayout>

<h1 class="text-3xl font-bold mb-8">

Auditoría

</h1>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">

<table class="w-full">

<thead class="bg-neutral-100">

<tr>

<th class="p-3">Fecha</th>
<th class="p-3">Usuario</th>
<th class="p-3">Módulo</th>
<th class="p-3">Acción</th>
<th class="p-3">Descripción</th>
<th class="p-3">IP</th>

</tr>

</thead>

<tbody>

<tr
v-for="item in audit"
:key="item.id"
class="border-t"
>

<td class="p-3">{{item.created_at}}</td>

<td class="p-3">{{item.username}}</td>

<td class="p-3">{{item.module}}</td>

<td class="p-3">{{item.action}}</td>

<td class="p-3">{{item.description}}</td>

<td class="p-3">{{item.ip}}</td>

</tr>

<tr
v-if="audit.length==0"
>

<td
colspan="6"
class="text-center p-8 text-neutral-500"
>

No existen registros.

</td>

</tr>

</tbody>

</table>

</div>

</MainLayout>

</template>

<script setup>

import {ref,onMounted} from "vue"

import MainLayout from "../components/layout/MainLayout.vue"

const audit=ref([])

async function load(){

const r=await fetch(
"http://localhost:8000/api/audit.php"
)

const j=await r.json()

audit.value=j.data??[]

}

onMounted(load)

</script>