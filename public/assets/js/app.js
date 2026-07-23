fetch('api/status.php')
    .then(response => response.json())
    .then(response => {

        const data = response.data;

        document.getElementById('status').innerHTML = `
            <div class="card">
                <h2>Estado</h2>

                <p><b>Jobs:</b> ${data.jobs}</p>

                <p><b>Conexiones:</b> ${data.connections}</p>

                <p><b>Archivos Subidos:</b> ${data.uploaded}</p>

                <p><b>Ejecuciones:</b> ${data.executions}</p>

                <p><b>En Cola:</b> ${data.queue}</p>

                <p><b>En Ejecución:</b> ${data.running}</p>
            </div>
        `;

    })
    .catch(error => {
        console.error(error);

        document.getElementById('status').innerHTML = `
            <div class="card">
                <h2>Error</h2>
                <p>No fue posible obtener el estado del sistema.</p>
            </div>
        `;
    });