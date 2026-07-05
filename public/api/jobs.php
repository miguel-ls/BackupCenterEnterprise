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

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;
use BackupCenter\Repositories\JobRepository;
use BackupCenter\Services\JobService;

use BackupCenter\Core\Application;
use BackupCenter\Services\BackupService;

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$repository = new JobRepository($db);
$service = new JobService($repository);

$application = new Application();

$backupService = $application->backupService();

switch ($_SERVER['REQUEST_METHOD']) {

    case 'GET':

        echo json_encode(
            $repository->getJobs()
        );
        exit;

    case 'POST':

        $action = $_GET['action'] ?? '';

        /*
        |--------------------------------------------------------------------------
        | Ejecutar trabajo
        |--------------------------------------------------------------------------
        */

        if ($action === 'run') {

            $data = json_decode(file_get_contents('php://input'), true);

            $job = $service->get(
                (int)$data['id']
            );

            if (!$job) {

                http_response_code(404);

                echo json_encode([
                    'success' => false,
                    'message' => 'Trabajo no encontrado.'
                ]);

                exit;
            }

$service->start(
    (int)$job['id']
);

$result = $backupService->run(
    (int)$job['id']
);

if ($result) {

    $service->success(
        (int)$job['id']
    );

} else {

    $service->error(
        (int)$job['id']
    );
}

            echo json_encode([
                'success' => true,
                'message' => 'Trabajo preparado para ejecución.',
                'job' => $job
            ]);

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | Crear trabajo
        |--------------------------------------------------------------------------
        */

        $data = json_decode(file_get_contents('php://input'), true);

        $id = $repository->createJob(
            $data['connection_id'] ?? null,
            $data['name'],
            $data['source'],
            $data['destination'],
            $data['schedule']
        );

        echo json_encode([
            'success' => true,
            'id' => $id
        ]);

        exit;

    case 'PUT':

        $data = json_decode(file_get_contents('php://input'), true);

        $repository->updateJob(
            (int)$data['id'],
            $data['connection_id'] ?? null,
            $data['name'],
            $data['source'],
            $data['destination'],
            $data['schedule']
        );

        echo json_encode([
            'success' => true
        ]);

        exit;

    case 'DELETE':

        $data = json_decode(file_get_contents('php://input'), true);

        $repository->deleteJob(
            (int)$data['id']
        );

        echo json_encode([
            'success' => true
        ]);

        exit;
}