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

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$repository = new JobRepository($db);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    echo json_encode(
        $repository->getJobs()
    );

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $data = json_decode(file_get_contents('php://input'), true);

    $repository->deleteJob((int)$data['id']);

    echo json_encode([
        'success' => true
    ]);

    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    $data = json_decode(file_get_contents('php://input'), true);

    $repository->updateJob(
        (int)$data['id'],
        $data['name'],
        $data['source'],
        $data['destination'],
        $data['schedule']
    );

    echo json_encode([
        'success' => true
    ]);

    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$id = $repository->createJob(
    $data['name'],
    $data['source'],
    $data['destination'],
    $data['schedule']
);

echo json_encode([
    'success' => true,
    'id' => $id
]);