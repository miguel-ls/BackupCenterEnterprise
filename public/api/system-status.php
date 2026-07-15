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

function serviceStatus(string $service): string
{
    if (PHP_OS_FAMILY !== 'Windows') {
        return 'No soportado';
    }

    $powershell = 'C:\\Windows\\System32\\WindowsPowerShell\\v1.0\\powershell.exe';

    $command =
        '"' .
        $powershell .
        '" -NoProfile -ExecutionPolicy Bypass -Command "Get-Service -Name ' .
        $service .
        ' | Select-Object -ExpandProperty Status"';

    $output = shell_exec($command);

    if ($output === null) {
        return 'Desconocido';
    }

    $status = strtoupper(trim($output));

    switch ($status) {

        case 'RUNNING':
            return 'Activo';

        case 'STOPPED':
            return 'Inactivo';

        case 'PAUSED':
            return 'Pausado';

        default:
            return 'Desconocido';
    }
}

$last = $history->latest(1);

$stats = $history->statistics();

$data = [

    "scheduler" => serviceStatus("BackupCenterScheduler"),

    "worker" => serviceStatus("BackupCenterWorker"),

    "queue" => $queue->countPending(),

    "running" => $queue->countRunning(),

    "failed" => $queue->countFailed(),

    "completed" => $queue->countCompleted(),

    "executions" => (int)($stats["total_runs"] ?? 0),

    "last_execution" => $last[0]["executed_at"] ?? "-"

];

echo json_encode([
    "success" => true,
    "data" => $data
]);