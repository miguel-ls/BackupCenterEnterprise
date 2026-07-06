<template>

<div class="p-8">

    <PageHeader
        title="Queue Monitor"
        subtitle="Monitoreo en tiempo real de la cola de trabajos."
    />

<QueueStatistics
    :items="queue"
/>

    <div v-if="loading">

        <Loading />

    </div>

    <div v-else-if="queue.length===0">

        <EmptyState
            message="No existen trabajos en la cola."
        />

    </div>

    <QueueTable
        v-else
        :items="queue"
    />

</div>

</template>

<script setup>

import { ref,onMounted,onUnmounted } from "vue";

import PageHeader from "@/components/common/PageHeader.vue";
import Loading from "@/components/common/Loading.vue";
import EmptyState from "@/components/common/EmptyState.vue";

import QueueTable from "@/components/queue/QueueTable.vue";

import QueueStatistics from "@/components/queue/QueueStatistics.vue";

const queue = ref([]);
const loading = ref(true);

async function load(){

    loading.value=true;

    const response=await fetch(
        "http://localhost:8000/api/job-queue.php"
    );

    queue.value=await response.json();

    loading.value=false;

}

let timer;

onMounted(()=>{

    load();

    timer=setInterval(load,2000);

});

onUnmounted(()=>{

    clearInterval(timer);

});

</script>