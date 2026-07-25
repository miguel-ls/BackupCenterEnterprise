<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/bootstrap.php';

use BackupCenter\Core\ApiResponse;
use BackupCenter\Services\AgentService;

$service = new AgentService($pdo);

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'register':
        $service->register();
        break;

    case 'exists':
        $service->exists();
        break;

    case 'register-file':
        $service->registerFile();
        break;
                
    default:
        ApiResponse::error('Invalid action');
}