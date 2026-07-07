const API = "http://localhost:8000/api";

async function request(endpoint, options = {}) {

    const response = await fetch(`${API}/${endpoint}`, {
        headers: {
            "Content-Type": "application/json"
        },
        ...options
    });

    return await response.json();

}

/* ================= DASHBOARD ================= */

export const getStatus = () => request("status.php");

export const getStatistics = () => request("statistics.php");

export const getVersion = () => request("version.php");

export const getChart = () => request("chart.php");

/* ================= SETTINGS ================= */

export const getSettings = () => request("settings.php");

export const updateSettings = (settings) =>
    request("settings.php", {
        method: "PUT",
        body: JSON.stringify(settings)
    });

/* ================= QUEUE ================= */

export const getQueue = () => request("job-queue.php");

/* ================= HISTORY ================= */

export const getHistory = () => request("history.php");

/* ================= CONNECTIONS ================= */

export const getConnections = () => request("connections.php");

export const createConnection = (connection) =>
    request("connections.php", {
        method: "POST",
        body: JSON.stringify(connection)
    });

export const updateConnection = (connection) =>
    request("connections.php", {
        method: "PUT",
        body: JSON.stringify(connection)
    });

export const deleteConnection = (id) =>
    request("connections.php", {
        method: "DELETE",
        body: JSON.stringify({ id })
    });

export const testConnection = (id) =>
    request("connections.php", {
        method: "POST",
        body: JSON.stringify({
            action: "test",
            id
        })
    });

/* ================= JOBS ================= */

export const getJobs = () => request("jobs.php");

export const createJob = (job) =>
    request("jobs.php", {
        method: "POST",
        body: JSON.stringify(job)
    });

export const updateJob = (job) =>
    request("jobs.php", {
        method: "PUT",
        body: JSON.stringify(job)
    });

export const deleteJob = (id) =>
    request("jobs.php", {
        method: "DELETE",
        body: JSON.stringify({ id })
    });

export const runJob = (id) =>
    request("jobs.php", {
        method: "POST",
        body: JSON.stringify({
            action: "run",
            id
        })
    });