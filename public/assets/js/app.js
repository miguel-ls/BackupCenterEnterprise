fetch('api/status.php')
.then(r=>r.json())
.then(data=>{

    document.getElementById('status').innerHTML=`

        <div class="card">

            <h2>Estado</h2>

            <p><b>Versión:</b> ${data.version}</p>

            <p><b>Servicio:</b> ${data.service}</p>

            <p><b>PHP:</b> ${data.php}</p>

            <p><b>Base de datos:</b> ${data.database}</p>

            <p><b>Hora:</b> ${data.time}</p>

        </div>

    `;

});