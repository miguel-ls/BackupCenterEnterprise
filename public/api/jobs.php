<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$pdo = $db->getConnection();

$stmt = $pdo->query("
    SELECT
        id,
        name,
        last_status AS status,
        COALESCE(last_run, '-') AS time
    FROM jobs
    ORDER BY id
");

echo json_encode(
    $stmt->fetchAll(PDO::FETCH_ASSOC)
);