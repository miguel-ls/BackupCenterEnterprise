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

export const getSystemStatus = () => request("system-status.php");

//export const getSystemInfo = () => request("system-info.php");

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

export const getHistory = (
    page = 1,
    limit = 5,
    clientId = 0
) =>
    request(
        `history.php?page=${page}&limit=${limit}&client_id=${clientId}`
    );

/* ================= USERS ================= */

export const getUsers = () =>
    request("users.php");

export const createUser = (user) =>
    request("users.php", {
        method: "POST",
        body: JSON.stringify(user)
    });

export const updateUser = (user) =>
    request("users.php", {
        method: "PUT",
        body: JSON.stringify(user)
    });

export const deleteUser = (id) =>
    request("users.php", {
        method: "DELETE",
        body: JSON.stringify({ id })
    });

/* ================= AUDIT ================= */

export const getAudit = () =>
    request("audit.php");

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

/* ================= LOGS ================= */

export const getLogs = (
    page = 1,
    limit = 50
) =>
    request(`logs.php?page=${page}&limit=${limit}`);

/* ================= NOTIFICATIONS ================= */

export const getNotifications = () =>
    request("notifications.php");

export const markNotificationsAsRead = () =>
    request("notifications.php", {
        method: "PUT"
    });

export const clearNotifications = () =>
    request("notifications.php", {
        method: "DELETE"
    });

/* ================= REPORTS ================= */

export const getReportSummary = () =>
    request("reports.php?action=summary");

export const getReportDaily = (days = 30) =>
    request(`reports.php?action=daily&days=${days}`);

export const getReportClients = () =>
    request("reports.php?action=clients");

export const getReportErrors = () =>
    request("reports.php?action=errors");

export const getReportJobs = () =>
    request("reports.php?action=jobs");

export const getReportConnections = () =>
    request("reports.php?action=connections");

/* ================= EXPORT ================= */

export const exportClientsExcel = () => {

    window.open(
        `${API}/reports-export.php?action=clients`,
        "_blank"
    );

};

export const exportJobsExcel = () => {

    window.open(
        `${API}/reports-export.php?action=jobs`,
        "_blank"
    );

};

export const exportConnectionsExcel = () => {

    window.open(
        `${API}/reports-export.php?action=connections`,
        "_blank"
    );

};

export const exportReport = (action, type = "excel") => {

    const format = type === "pdf" ? "pdf" : "excel";

    window.open(
        `${API}/reports-export.php?action=${encodeURIComponent(action)}&type=${format}`,
        "_blank"
    );

};

/*
|--------------------------------------------------------------------------
| CLIENTS
|--------------------------------------------------------------------------
*/

export const getClients = () =>
    request("clients.php");

export const createClient = (client) =>
    request("clients.php", {
        method: "POST",
        body: JSON.stringify(client)
    });

export const updateClient = (client) =>
    request("clients.php", {
        method: "PUT",
        body: JSON.stringify(client)
    });

export const deleteClient = (id) =>
    request("clients.php", {
        method: "DELETE",
        body: JSON.stringify({ id })
    });
