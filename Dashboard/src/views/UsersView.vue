<template>

<MainLayout>

<div class="flex justify-between items-center mb-8">

    <h1 class="text-3xl font-bold">

        Usuarios

    </h1>

    <button
        @click="newUser"
        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg"
    >

        Nuevo Usuario

    </button>

</div>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">

<table class="w-full">

<thead class="bg-neutral-100">

<tr>

<th class="p-3 text-left">Usuario</th>
<th class="p-3 text-left">Nombre</th>
<th class="p-3 text-left">Rol</th>
<th class="p-3 text-left">Estado</th>
<th class="p-3 text-left">Último acceso</th>
<th class="p-3 text-center">Acciones</th>

</tr>

</thead>

<tbody>

<tr
v-for="user in users"
:key="user.id"
class="border-t hover:bg-neutral-50"
>

<td class="p-3">{{user.username}}</td>

<td class="p-3">{{user.fullname}}</td>

<td class="p-3">

<span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">

{{user.role}}

</span>

</td>

<td class="p-3">

<span :class="user.enabled?'text-green-600':'text-red-600'">

{{user.enabled?'Activo':'Inactivo'}}

</span>

</td>

<td class="p-3">

{{user.last_login??'-'}}

</td>

<td class="p-3 text-center">

<button
@click="editUser(user)"
class="text-blue-600 mr-4 hover:underline"
>

Editar

</button>

<button
@click="deleteUser(user)"
class="text-red-600 hover:underline"
>

Eliminar

</button>

</td>

</tr>

</tbody>

</table>

</div>

<div
v-if="show"
class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
>

<div class="bg-white rounded-xl w-[500px] p-8">

<h2 class="text-2xl font-bold mb-6">

{{editing?'Editar Usuario':'Nuevo Usuario'}}

</h2>

<div class="space-y-4">

<input
v-model="form.username"
placeholder="Usuario"
:disabled="editing"
class="w-full border rounded-lg p-3"
/>

<input
v-model="form.fullname"
placeholder="Nombre Completo"
class="w-full border rounded-lg p-3"
/>

<input
v-model="form.password"
type="password"
:placeholder="editing?'Nueva contraseña (opcional)':'Contraseña'"
class="w-full border rounded-lg p-3"
/>

<select
v-model="form.role"
class="w-full border rounded-lg p-3"
>

<option>ADMIN</option>
<option>OPERATOR</option>
<option>VIEWER</option>

</select>

<label class="flex items-center gap-3">

<input
type="checkbox"
v-model="form.enabled"
/>

Activo

</label>

</div>

<div class="flex justify-end gap-3 mt-8">

<button
@click="show=false"
class="px-5 py-2 rounded-lg border"
>

Cancelar

</button>

<button
@click="save"
class="px-5 py-2 rounded-lg bg-blue-600 text-white"
>

{{editing?'Actualizar':'Guardar'}}

</button>

</div>

</div>

</div>

</MainLayout>

</template>

<script setup>

import {ref,onMounted} from "vue"

import MainLayout from "../components/layout/MainLayout.vue"

const users=ref([])

const show=ref(false)

const editing=ref(false)

const form=ref({})

function resetForm(){

form.value={

id:null,

username:"",

fullname:"",

password:"",

role:"VIEWER",

enabled:true

}

}

resetForm()

async function load(){

const r=await fetch("http://localhost:8000/api/users.php")

const j=await r.json()

users.value=j.data??[]

}

function newUser(){

editing.value=false

resetForm()

show.value=true

}

function editUser(user){

editing.value=true

form.value={

...user,

password:""

}

show.value=true

}

async function save(){

const method=editing.value?"PUT":"POST"

const r=await fetch(

"http://localhost:8000/api/users.php",

{

method,

headers:{

"Content-Type":"application/json"

},

body:JSON.stringify(form.value)

}

)

const j=await r.json()

alert(j.message)

if(j.success){

show.value=false

load()

}

}

async function deleteUser(user){

if(user.id===1){

alert("No puede eliminar el administrador.")

return

}

if(!confirm("¿Eliminar usuario?")){

return

}

const r=await fetch(

"http://localhost:8000/api/users.php",

{

method:"DELETE",

headers:{

"Content-Type":"application/json"

},

body:JSON.stringify({

id:user.id

})

}

)

const j=await r.json()

alert(j.message)

load()

}

onMounted(load)

</script>