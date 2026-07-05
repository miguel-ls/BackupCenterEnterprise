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
use BackupCenter\Repositories\ConnectionRepository;

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$repository = new ConnectionRepository($db);

switch ($_SERVER['REQUEST_METHOD']) {

    /*
    |--------------------------------------------------------------------------
    | Obtener conexiones
    |--------------------------------------------------------------------------
    */

    case 'GET':

        echo json_encode(
            $repository->getAll()
        );

        exit;

    /*
    |--------------------------------------------------------------------------
    | Crear conexión
    |--------------------------------------------------------------------------
    */

    case 'POST':

        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        $id = $repository->create(
            $data['name'],
            $data['host'],
            (int)$data['port'],
            $data['username'],
            $data['password'],
            $data['hostkey'],
            $data['protocol'],
            $data['remote_path']
        );

        echo json_encode([
            'success' => true,
            'id' => $id
        ]);

        exit;

    /*
    |--------------------------------------------------------------------------
    | Actualizar
    |--------------------------------------------------------------------------
    */

    case 'PUT':

        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        $repository->update(
            (int)$data['id'],
            $data['name'],
            $data['host'],
            (int)$data['port'],
            $data['username'],
            $data['password'],
            $data['hostkey'],
            $data['protocol'],
            $data['remote_path']
        );

        echo json_encode([
            'success' => true
        ]);

        exit;

    /*
    |--------------------------------------------------------------------------
    | Eliminar
    |--------------------------------------------------------------------------
    */

    case 'DELETE':

        $data = json_decode(
            file_get_contents('php://input'),
            true
        );

        $repository->delete(
            (int)$data['id']
        );

        echo json_encode([
            'success' => true
        ]);

        exit;
}