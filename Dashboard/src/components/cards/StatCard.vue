<template>
  <div class="bg-white rounded-xl border border-neutral-200 p-6 shadow-sm hover:shadow-lg transition">

    <div class="text-sm text-neutral-500 font-medium">
      {{ title }}
    </div>

    <div class="mt-4 flex items-center justify-between">

      <div class="text-3xl font-bold text-neutral-800">
        {{ value }}
      </div>

      <div
        class="w-12 h-12 rounded-xl flex items-center justify-center"
        :class="iconColor"
      >
        <span class="text-xl">{{ icon }}</span>
      </div>

    </div>

  </div>
</template>

<script setup>

import { computed } from 'vue'

const props = defineProps({

    title: String,

    value: [String, Number]

})

const icon = computed(() => {

    switch(props.title){

        case 'Versión': return '📦'

        case 'Servicio': return '🟢'

        case 'PHP': return '⚙️'

        case 'Base de datos': return '🗄️'

        default: return '📊'

    }

})

const iconColor = computed(()=>{

    switch(props.title){

        case 'Versión':
            return 'bg-blue-100'

        case 'Servicio':
            return 'bg-green-100'

        case 'PHP':
            return 'bg-orange-100'

        case 'Base de datos':
            return 'bg-violet-100'

        default:
            return 'bg-neutral-100'

    }

})

</script>