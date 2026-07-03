const API_URL = 'http://localhost:8000/api'

export async function getStatus() {

    const response = await fetch(`${API_URL}/status.php`)

    if (!response.ok) {
        throw new Error('No fue posible obtener el estado del servidor.')
    }

    return await response.json()

}

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
