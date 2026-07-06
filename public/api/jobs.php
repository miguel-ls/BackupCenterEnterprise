<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;
use BackupCenter\Repositories\JobRepository;
use BackupCenter\Core\Application;

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$app = new Application();

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$repository = new JobRepository($db);

$method = $_SERVER['REQUEST_METHOD'];

$body = json_decode(
    file_get_contents('php://input'),
    true
);

if (!$body) {
    $body = [];
}

switch ($method) {

    case 'GET':

        echo json_encode([
            'success' => true,
            'data' => $repository->getJobs()
        ]);

        break;

case 'POST':

    if (($body['action'] ?? '') === 'run') {

        $ok = $app
            ->backupService()
            ->run((int)$body['id']);

        echo json_encode([
            'success' => $ok,
            'message' => $ok
                ? 'Trabajo ejecutado correctamente.'
                : 'No fue posible ejecutar el trabajo.'
        ]);

        break;
    }

    $id = $repository->createJob(
        !empty($body['connection_id']) ? (int)$body['connection_id'] : null,
        $body['name'] ?? '',
        $body['source'] ?? '',
        $body['destination'] ?? '',
        $body['schedule'] ?? ''
    );

    echo json_encode([
        'success' => true,
        'id' => $id
    ]);

    break;
    
case 'PUT':

    $ok = $repository->updateJob(
        (int)$body['id'],
        !empty($body['connection_id']) ? (int)$body['connection_id'] : null,
        $body['name'] ?? '',
        $body['source'] ?? '',
        $body['destination'] ?? '',
        $body['schedule'] ?? ''
    );

    echo json_encode([
        'success' => $ok
    ]);

    break;
    
case 'DELETE':

    $ok = $repository->deleteJob(
        (int)$body['id']
    );

    echo json_encode([
        'success' => $ok
    ]);

    break;
        
    default:

        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);

}