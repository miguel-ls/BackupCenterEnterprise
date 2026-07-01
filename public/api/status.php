<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;
use BackupCenter\Repositories\UploadedFileRepository;

header('Content-Type: application/json; charset=utf-8');

try {

    $database = new Database(
        Paths::database() . '/backupcenter.db'
    );

    $repository = new UploadedFileRepository($database);

    echo json_encode([
        'success' => true,
        'version' => trim(file_get_contents(__DIR__ . '/../../VERSION')),
        'service' => 'running',
        'database' => file_exists(Paths::database() . '/backupcenter.db'),
        'php' => PHP_VERSION,
        'time' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);

}