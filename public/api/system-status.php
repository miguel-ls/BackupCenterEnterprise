<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$pdo = $db->getConnection();

$data = [

    /*
     * En la siguiente versión estos valores
     * vendrán del Servicio Windows.
     */
    'scheduler' => 'Activo',

    'worker' => 'Activo',

    /*
     * Cola
     */

    'queue' => (int)$pdo->query("
        SELECT COUNT(*)
        FROM job_queue
        WHERE status='Pending'
    ")->fetchColumn(),

    'running' => (int)$pdo->query("
        SELECT COUNT(*)
        FROM job_queue
        WHERE status='Running'
    ")->fetchColumn(),

    'failed' => (int)$pdo->query("
        SELECT COUNT(*)
        FROM job_queue
        WHERE status='Failed'
    ")->fetchColumn(),

    /*
     * Historial
     */

    'executions' => (int)$pdo->query("
        SELECT COUNT(*)
        FROM execution_history
    ")->fetchColumn(),

    'last_execution' => $pdo->query("
        SELECT MAX(executed_at)
        FROM execution_history
    ")->fetchColumn()

];

echo json_encode([
    'success' => true,
    'data' => $data
]);