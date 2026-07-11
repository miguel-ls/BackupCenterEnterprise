<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {

    http_response_code(200);

    exit;

}

require_once __DIR__ . '/bootstrap.php';

use BackupCenter\Core\ApiResponse;

$status = [

    'jobs' => (int)$pdo->query("
        SELECT COUNT(*)
        FROM jobs
    ")->fetchColumn(),

    'connections' => (int)$pdo->query("
        SELECT COUNT(*)
        FROM connections
    ")->fetchColumn(),

    'uploaded' => (int)$pdo->query("
        SELECT COUNT(*)
        FROM uploaded_files
    ")->fetchColumn(),

    'executions' => (int)$pdo->query("
        SELECT COUNT(*)
        FROM execution_history
    ")->fetchColumn(),

    'queue' => (int)$pdo->query("
        SELECT COUNT(*)
        FROM job_queue
        WHERE status='Pending'
    ")->fetchColumn(),

    'running' => (int)$pdo->query("
        SELECT COUNT(*)
        FROM job_queue
        WHERE status='Running'
    ")->fetchColumn()

];

ApiResponse::success($status);