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
use BackupCenter\Core\Audit;
use BackupCenter\Repositories\ConnectionRepository;

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$repository = new ConnectionRepository($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

case 'GET':

    echo json_encode([
        'success' => true,
        'data' => $repository->getAll()
    ]);

break;

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

        $data['hostkey'] ?? '',

        $data['protocol'] ?? 'SFTP',

        $data['remote_path']

    );

    Audit::info(

        "CONNECTIONS",

        "CREATE",

        "Conexión ".$data['name']." creada",

        "admin"

    );

    echo json_encode([

        'success'=>true,

        'id'=>$id

    ]);

break;

case 'PUT':

    $data=json_decode(
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

        $data['hostkey'] ?? '',

        $data['protocol'] ?? 'SFTP',

        $data['remote_path']

    );

    Audit::info(

        "CONNECTIONS",

        "UPDATE",

        "Conexión ".$data['name']." actualizada",

        "admin"

    );

    echo json_encode([

        'success'=>true

    ]);

break;

case 'DELETE':

    $data=json_decode(
        file_get_contents('php://input'),
        true
    );

    $repository->delete(

        (int)$data['id']

    );

    Audit::info(

        "CONNECTIONS",

        "DELETE",

        "Conexión ID ".$data['id']." eliminada",

        "admin"

    );

    echo json_encode([

        'success'=>true

    ]);

break;

default:

    http_response_code(405);

    echo json_encode([

        'success'=>false,

        'message'=>'Método no permitido'

    ]);

}