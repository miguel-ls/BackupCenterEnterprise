<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/bootstrap.php';

use BackupCenter\Core\Application;
use BackupCenter\Services\CronService;

$app = new Application();

$db = $app->database()->getConnection();

$jobs = (int)$db->query("
    SELECT COUNT(*) FROM jobs
")->fetchColumn();

$connections = (int)$db->query("
    SELECT COUNT(*) FROM connections
")->fetchColumn();

$lastBackup = $db->query("
    SELECT executed_at
    FROM execution_history
    ORDER BY executed_at DESC
    LIMIT 1
")->fetchColumn();

$cron = new CronService();

$nextBackup = null;

$schedules = $db->query("
    SELECT schedule
    FROM jobs
    WHERE enabled=1
")->fetchAll(PDO::FETCH_COLUMN);

foreach ($schedules as $schedule) {

    $nextRun = $cron->nextRun($schedule);

    if ($nextRun !== null && ($nextBackup === null || $nextRun < $nextBackup)) {
        $nextBackup = $nextRun;
    }

}

echo json_encode([

    "success"=>true,

    "data"=>[

        "clients"=>$jobs,

        "repositories"=>$connections,

        "last_backup"=>$lastBackup ?? "-",

        "next_backup"=>$nextBackup ?? "-",

        "health"=>98

    ]

]);
