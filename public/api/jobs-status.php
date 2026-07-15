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

$cron = new CronService();

$rows = $db->query("
SELECT
    id,
    name,
    schedule,
    enabled,
    running,
    last_run,
    last_status
FROM jobs
ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as &$row) {

    $row["next_run"] = $cron->nextRun($row["schedule"]);

    $row["remaining"] = $cron->remaining($row["schedule"]);

    $row["due"] = $cron->isDue($row["schedule"]);

}

echo json_encode([
    "success" => true,
    "data" => $rows
]);