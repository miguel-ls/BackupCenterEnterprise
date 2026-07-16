<template>

<MainLayout>

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

        <div v-else-if="queue.length === 0">

            <EmptyState
                message="No existen trabajos en la cola."
            />

        </div>

        <QueueTable
            v-else
            :items="queue"
        />

    </div>

</MainLayout>

</template>

<script setup>

import {
    ref,
    onMounted,
    onUnmounted
} from "vue";

import MainLayout from "@/components/layout/MainLayout.vue";

import PageHeader from "@/components/common/PageHeader.vue";
import Loading from "@/components/common/Loading.vue";
import EmptyState from "@/components/common/EmptyState.vue";

import QueueTable from "@/components/queue/QueueTable.vue";
import QueueStatistics from "@/components/queue/QueueStatistics.vue";

import {
    getQueue
} from "@/api/client";

const queue = ref([]);
const loading = ref(true);

async function load(){

    loading.value = true;

    try{

        const response = await getQueue();

        queue.value = response.data ?? response;

    }catch(error){

        console.error(error);

        queue.value = [];

    }finally{

        loading.value = false;

    }

}

let timer = null;

onMounted(()=>{

    load();

    timer = setInterval(load,8000);

});

onUnmounted(()=>{

    if(timer){

        clearInterval(timer);

    }

});

</script>