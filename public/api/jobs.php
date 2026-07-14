<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Application;
use BackupCenter\Core\Audit;
use BackupCenter\Repositories\JobRepository;
use BackupCenter\Repositories\JobQueueRepository;

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$app = new Application();

$db = $app->database();

$repository = new JobRepository($db);

$queue = new JobQueueRepository($db);

$queue->initialize();

$method = $_SERVER['REQUEST_METHOD'];

$body = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($body)) {
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

        /*
        |--------------------------------------------------------------------------
        | Ejecutar trabajo
        |--------------------------------------------------------------------------
        */

        if (($body['action'] ?? '') === 'run') {

            $ok = $queue->enqueue(
                (int)$body['id']
            );

            if ($ok) {

                Audit::info(
                    "JOBS",
                    "QUEUE",
                    "Trabajo {$body['id']} agregado a la cola.",
                    "admin"
                );

                echo json_encode([
                    "success" => true,
                    "message" => "Trabajo agregado a la cola."
                ]);

            } else {

                echo json_encode([
                    "success" => false,
                    "message" => "El trabajo ya estaba en cola o ejecutándose."
                ]);

            }

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | Crear trabajo
        |--------------------------------------------------------------------------
        */

        $id = $repository->createJob(

            !empty($body['connection_id'])
                ? (int)$body['connection_id']
                : null,

            $body['name'] ?? '',

            $body['source'] ?? '',

            $body['destination'] ?? '',

            $body['schedule'] ?? ''

        );

        Audit::info(
            "JOBS",
            "CREATE",
            "Trabajo " . ($body['name'] ?? '') . " creado",
            "admin"
        );

        echo json_encode([
            "success" => true,
            "id" => $id
        ]);

        break;

    case 'PUT':

        $ok = $repository->updateJob(

            (int)$body['id'],

            !empty($body['connection_id'])
                ? (int)$body['connection_id']
                : null,

            $body['name'] ?? '',

            $body['source'] ?? '',

            $body['destination'] ?? '',

            $body['schedule'] ?? ''

        );

        Audit::info(
            "JOBS",
            "UPDATE",
            "Trabajo " . ($body['name'] ?? '') . " actualizado",
            "admin"
        );

        echo json_encode([
            "success" => $ok
        ]);

        break;

    case 'DELETE':

        $ok = $repository->deleteJob(
            (int)$body['id']
        );

        Audit::info(
            "JOBS",
            "DELETE",
            "Trabajo {$body['id']} eliminado",
            "admin"
        );

        echo json_encode([
            "success" => $ok
        ]);

        break;

    default:

        http_response_code(405);

        echo json_encode([
            "success" => false,
            "message" => "Método no permitido"
        ]);
}