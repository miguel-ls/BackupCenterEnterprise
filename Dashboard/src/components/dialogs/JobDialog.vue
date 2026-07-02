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
                @click="$emit('update:modelValue', false)"
                class="text-2xl"
            >
                ×
            </button>

        </div>

        <JobForm />

    </div>

</div>

</template>

<script setup>

import JobForm from '../jobs/JobForm.vue'

defineProps({

    modelValue:Boolean

})

defineEmits([

    'update:modelValue'

])

</script>