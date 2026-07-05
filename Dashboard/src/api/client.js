const API_URL = 'http://localhost:8000/api'

export async function getStatus() {

    const response = await fetch(`${API_URL}/status.php`)

    if (!response.ok) {
        throw new Error('No fue posible obtener el estado del servidor.')
    }

    return await response.json()

}

/* ==========================================================
   JOBS
========================================================== */

export async function getJobs(){

    const response = await fetch(`${API_URL}/jobs.php`)

    return await response.json()

}

export async function createJob(job){

    const response = await fetch(`${API_URL}/jobs.php`,{

        method:'POST',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify(job)

    })

    return await response.json()

}

export async function updateJob(job){

    const response = await fetch(`${API_URL}/jobs.php`,{

        method:'PUT',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify(job)

    })

    return await response.json()

}

export async function deleteJob(id){

    const response = await fetch(`${API_URL}/jobs.php`,{

        method:'DELETE',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify({
            id
        })

    })

    return await response.json()

}

export async function runJob(id){

    const response = await fetch(`${API_URL}/jobs.php?action=run`,{

        method:'POST',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify({
            id
        })

    })

    return await response.json()

}

/* ==========================================================
   CONNECTIONS
========================================================== */

export async function getConnections(){

    const response = await fetch(`${API_URL}/connections.php`)

    return await response.json()

}

export async function createConnection(connection){

    const response = await fetch(`${API_URL}/connections.php`,{

        method:'POST',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify(connection)

    })

    return await response.json()

}

export async function updateConnection(connection){

    const response = await fetch(`${API_URL}/connections.php`,{

        method:'PUT',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify(connection)

    })

    return await response.json()

}

export async function deleteConnection(id){

    const response = await fetch(`${API_URL}/connections.php`,{

        method:'DELETE',

        headers:{
            'Content-Type':'application/json'
        },

        body:JSON.stringify({
            id
        })

    })

    return await response.json()

}