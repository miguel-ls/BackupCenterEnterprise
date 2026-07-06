<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;
use BackupCenter\Repositories\JobQueueRepository;

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$repository = new JobQueueRepository($db);

switch ($_SERVER['REQUEST_METHOD']) {

case 'GET':

    $rows = $repository->getAll();

    echo json_encode([
        'success' => true,
        'count' => count($rows),
        'data' => $rows
    ]);

    exit;
}