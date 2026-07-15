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

use BackupCenter\Core\Application;
use BackupCenter\Repositories\ReportRepository;

$app = new Application();

$repository = new ReportRepository(
    $app->database()
);

$action = $_GET['action'] ?? 'summary';

switch ($action) {

    case 'summary':

        $data = $repository->summary();

        break;

    case 'daily':

        $days = isset($_GET['days'])
            ? (int)$_GET['days']
            : 30;

        $data = $repository->daily($days);

        break;

    case 'clients':

        $data = $repository->clients();

        break;

    case 'jobs':

        $data = $repository->jobs();

        break;

    case 'connections':

        $data = $repository->connections();

        break;

    default:

        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Acción no válida."
        ]);

        exit;
}

echo json_encode([

    "success" => true,

    "action" => $action,

    "generated_at" => date('Y-m-d H:i:s'),

    "data" => $data

]);