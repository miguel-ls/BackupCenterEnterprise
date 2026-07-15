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
use BackupCenter\Services\WindowsServiceMonitor;

$app = new Application();

$queue = new JobQueueRepository(
    $app->database()
);

$history = new ExecutionHistoryRepository(
    $app->database()
);

$queue->initialize();

$monitor = new WindowsServiceMonitor();

$last = $history->latest(1);

$stats = $history->statistics();

$scheduler = $monitor->getServiceInfo(
    "BackupCenterScheduler"
);

$worker = $monitor->getServiceInfo(
    "BackupCenterWorker"
);

echo json_encode([

    "success"=>true,

    "data"=>[

        "scheduler"=>$scheduler,

        "worker"=>$worker,

        "queue"=>$queue->countPending(),

        "running"=>$queue->countRunning(),

        "failed"=>$queue->countFailed(),

        "completed"=>$queue->countCompleted(),

        "executions"=>(int)($stats["total_runs"] ?? 0),

        "last_execution"=>$last[0]["executed_at"] ?? "-"

    ]

]);