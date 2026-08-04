<template>
<DocumentationLayout>
    <div class="documentation">

        <div class="hero">

            <div>

                <h1>📚 BackupCenter Enterprise</h1>

                <p class="subtitle">

                    Centro de Documentación Técnica

                </p>

                <p class="description">

                    Documentación oficial del proyecto BackupCenter Enterprise.

                    Aquí encontrará arquitectura, API, Docker, Base de Datos,

                    Agente Windows, procedimientos de despliegue y manuales técnicos.

                </p>

            </div>

        </div>

        <div class="grid">

        <DocCard @click="open('/documentation/development')">

            <template #icon>
                🚀
            </template>

            <template #title>
                Primeros pasos
            </template>

            Instalación, Git, entorno de desarrollo y despliegue.

        </DocCard>

            <DocCard @click="open('/documentation/architecture')">

                <template #icon>
                    🏗
                </template>

                <template #title>
                    Arquitectura
                </template>

                Diseño general del sistema y comunicación entre componentes.

            </DocCard>

            <DocCard @click="open('/documentation/database')">

                <template #icon>
                    💾
                </template>

                <template #title>
                    Base de Datos
                </template>

                Modelo de datos, tablas, índices y relaciones.

            </DocCard>

            <div class="doc-card">

                <div class="icon">🌐</div>

                <h2>API</h2>

                <p>

                    Endpoints REST utilizados por Dashboard y Agente.

                </p>

            </div>

            <div class="doc-card">

                <div class="icon">🤖</div>

                <h2>Agente</h2>

                <p>

                    Registro, Scheduler, Heartbeat y sincronización.

                </p>

            </div>

            <div class="doc-card">

                <div class="icon">🐳</div>

                <h2>Docker</h2>

                <p>

                    Sandbox, Producción, Docker Compose y Dockerfile.

                </p>

            </div>

        </div>

    </div>
</DocumentationLayout>
</template>

<script setup>

import { useRouter } from "vue-router";
import DocumentationLayout from "../layouts/DocumentationLayout.vue"
import DocCard from "../components/DocCard.vue";

const router = useRouter();

function open(path) {
    router.push(path);
}

</script>

<style scoped>

.documentation{

    padding:40px;

}

.hero{

    background:white;

    border-radius:16px;

    padding:40px;

    margin-bottom:35px;

    box-shadow:0 5px 18px rgba(0,0,0,.06);

}

.hero h1{

    margin:0;

    font-size:38px;

}

.subtitle{

    color:#1976d2;

    font-size:22px;

    font-weight:600;

    margin-top:10px;

}

.description{

    margin-top:20px;

    color:#555;

    max-width:900px;

    line-height:1.8;

}

.grid{

    display:grid;

    grid-template-columns:repeat(auto-fill,minmax(320px,1fr));

    gap:24px;

}

.doc-card{

    background:white;

    border-radius:16px;

    padding:30px;

    box-shadow:0 4px 16px rgba(0,0,0,.06);

    transition:.25s;

    cursor:pointer;

}

.doc-card:hover{

    transform:translateY(-6px);

    box-shadow:0 12px 25px rgba(0,0,0,.10);

}

.icon{

    font-size:42px;

    margin-bottom:18px;

}

.doc-card h2{

    margin:0 0 15px;

}

.doc-card p{

    color:#666;

    line-height:1.6;

}

</style>