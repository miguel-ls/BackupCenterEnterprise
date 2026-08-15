<?php

require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/bootstrap.php';

use BackupCenter\Core\ApiResponse;
use BackupCenter\Core\AgentAuth;
use BackupCenter\Core\ApiController;
use BackupCenter\Repositories\AgentRepository;
use BackupCenter\Services\AgentService;

$service = new AgentService($pdo);

$action = $_GET['action'] ?? '';

if (
    in_array($action, ['exists', 'register-file', 'execution-history'], true)
    && array_key_exists('HTTP_AUTHORIZATION', $_SERVER)
) {
    AgentAuth::validateAgentToken(new AgentRepository($pdo));
}

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