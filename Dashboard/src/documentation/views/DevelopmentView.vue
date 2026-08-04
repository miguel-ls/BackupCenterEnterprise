<template>
<DocumentationLayout>
<div class="page">

    <DocTitle
        title="Entorno de Desarrollo"
        subtitle="Arquitectura, flujo de trabajo y publicación de BackupCenter Enterprise"
    >

        <template #icon>
            🚀
        </template>

    </DocTitle>

    <DocAlert
        type="info"
        title="Entorno oficial de desarrollo">

        Toda modificación del proyecto debe validarse primero en el
        ambiente Sandbox antes de publicarse en Producción.

    </DocAlert>

    <DocSection title="Arquitectura">

    <pre>
    Laptop
        │
        ▼
    GitHub
        ▲
        │
    Code Server
        │
        ▼
    Sandbox
        │
        ▼
    Producción
    </pre>

    </DocSection>

    <DocSection title="Flujo recomendado">

    <ol>

        <li>Desarrollar.</li>

        <li>Probar.</li>

        <li>Commit.</li>

        <li>Push.</li>

        <li>Actualizar Sandbox.</li>

        <li>Validar.</li>

        <li>Actualizar Producción.</li>

    </ol>

    <DocCode language="bash">
    git pull origin feature/BC-070-scheduler

    npm install

    npm run build -- --watch

    cd /config/workspace

    ./publish-dashboard.sh
    </DocCode>

    </DocSection>

    <DocSection title="Escenarios de Desarrollo">

        <p>

            BackupCenter Enterprise admite tres escenarios oficiales de desarrollo,
            cada uno orientado a una etapa diferente del ciclo de vida del software.

        </p>

        <table class="doc-table">

            <thead>

                <tr>

                    <th>Escenario</th>
                    <th>Objetivo</th>
                    <th>Ubicación</th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td>💻 Local</td>

                    <td>Desarrollo desde Visual Studio Code.</td>

                    <td>Laptop del desarrollador.</td>

                </tr>

                <tr>

                    <td>☁️ Code Server</td>

                    <td>Desarrollo remoto sincronizado con el servidor.</td>

                    <td>Contenedor Code Server.</td>

                </tr>

                <tr>

                    <td>🚀 Producción</td>

                    <td>Aplicación utilizada por los usuarios finales.</td>

                    <td>Contenedor BackupCenter.</td>

                </tr>

            </tbody>

        </table>

    </DocSection>

</div>
</DocumentationLayout>
</template>

<script setup>

import DocumentationLayout from "../layouts/DocumentationLayout.vue"
import DocTitle from "../components/DocTitle.vue"
import DocSection from "../components/DocSection.vue"
import DocAlert from "../components/DocAlert.vue"
import DocCode from "../components/DocCode.vue"

</script>

<style scoped>

.page{

    padding:40px;

}

.lead{

    font-size:18px;

    color:#666;

    margin-bottom:35px;

}

.section{

    background:white;

    padding:30px;

    margin-bottom:30px;

    border-radius:16px;

    box-shadow:0 5px 18px rgba(0,0,0,.06);

}

pre{

    background:#1f2937;

    color:white;

    padding:25px;

    border-radius:10px;

    overflow:auto;

}

.doc-table{

    width:100%;
    border-collapse:collapse;
    margin-top:20px;

}

.doc-table th{

    background:#2563eb;
    color:white;
    padding:14px;
    text-align:left;

}

.doc-table td{

    border-bottom:1px solid #e5e7eb;
    padding:14px;

}

.doc-table tr:hover{

    background:#f9fafb;

}

</style>