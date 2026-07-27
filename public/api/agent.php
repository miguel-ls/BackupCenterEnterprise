<?php

require_once __DIR__ . '/cors.php';
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
                
    case 'execution-history':
        $service->executionHistory();
        break;

    default:
        ApiResponse::error('Invalid action');
}