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

use BackupCenter\Core\Application;
use BackupCenter\Repositories\JobQueueRepository;
use BackupCenter\Repositories\ExecutionHistoryRepository;

$app = new Application();

$queue = new JobQueueRepository(
    $app->database()
);

$history = new ExecutionHistoryRepository(
    $app->database()
);

$queue->initialize();

$last = $history->latest(1);

$data = [

    "scheduler" => "Activo",

    "worker" => "Activo",

    "queue" => $queue->countPending(),

    "running" => $queue->countRunning(),

    "failed" => $queue->countFailed(),

    "completed" => $queue->countCompleted(),

    "executions" => $history->statistics()["total_runs"] ?? 0,

    "last_execution" => $last[0]["executed_at"] ?? "-"

];

echo json_encode([

    "success" => true,

    "data" => $data

]);