<template>

<div
    v-if="modelValue"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
>

    <div class="bg-white rounded-xl shadow-xl w-[700px] p-6">

        <div class="flex justify-between items-center mb-6">

            <h2 class="text-2xl font-bold">
                Nuevo trabajo
            </h2>

            <button
                @click="close"
                class="text-2xl"
            >
                ×
            </button>

        </div>

        <JobForm
            :job="props.job"
            @saved="saved"
        />
        
    </div>

</div>

</template>

<script setup>

import JobForm from '../jobs/JobForm.vue'

const emit = defineEmits([
    'update:modelValue',
    'saved'
])

const props = defineProps({

    modelValue: Boolean,

    job: Object

})

function close(){

    emit('update:modelValue',false)

}

function saved(){

    emit('saved')

    emit('update:modelValue',false)

}

</script>