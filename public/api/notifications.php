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
use BackupCenter\Repositories\NotificationRepository;

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$repository = new NotificationRepository($db);

echo json_encode([
    'success' => true,
    'data' => $repository->getAll()
]);