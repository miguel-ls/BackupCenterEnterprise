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

$stmt = $pdo->query("
SELECT
    date(executed_at) AS day,
    COUNT(*) AS executions,
    SUM(files_uploaded) AS uploaded
FROM execution_history
WHERE executed_at >= date('now','-6 day')
GROUP BY date(executed_at)
ORDER BY day
");

echo json_encode([
    'success' => true,
    'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);