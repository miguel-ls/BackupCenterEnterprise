<?php

require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$pdo = $db->getConnection();

$stmt = $pdo->query("
SELECT
    date(started_at) AS day,
    COUNT(*) AS executions,
    COALESCE(SUM(files_uploaded),0) AS uploaded
FROM execution_history
WHERE started_at >= date('now','-6 day')
GROUP BY date(started_at)
ORDER BY day
");

echo json_encode([
    'success' => true,
    'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);