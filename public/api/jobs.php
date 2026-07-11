<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Application;
use BackupCenter\Core\Audit;
use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;
use BackupCenter\Repositories\JobRepository;

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

        if($ok){

            Audit::info(
                "JOBS",
                "RUN",
                "Trabajo ID ".$body['id']." ejecutado",
                "admin"
            );

        }else{

            Audit::error(
                "JOBS",
                "RUN",
                "Error ejecutando trabajo ID ".$body['id'],
                "admin"
            );

        }

        echo json_encode([
            'success'=>$ok,
            'message'=>$ok
                ?'Trabajo ejecutado correctamente.'
                :'No fue posible ejecutar el trabajo.'
        ]);

        break;
    }

    $id=$repository->createJob(

        !empty($body['connection_id'])
            ?(int)$body['connection_id']
            :null,

        $body['name'] ?? '',

        $body['source'] ?? '',

        $body['destination'] ?? '',

        $body['schedule'] ?? ''

    );

    Audit::info(

        "JOBS",

        "CREATE",

        "Trabajo ".$body['name']." creado",

        "admin"

    );

    echo json_encode([

        'success'=>true,

        'id'=>$id

    ]);

break;

case 'PUT':

    $ok=$repository->updateJob(

        (int)$body['id'],

        !empty($body['connection_id'])
            ?(int)$body['connection_id']
            :null,

        $body['name'] ?? '',

        $body['source'] ?? '',

        $body['destination'] ?? '',

        $body['schedule'] ?? ''

    );

    Audit::info(

        "JOBS",

        "UPDATE",

        "Trabajo ".$body['name']." actualizado",

        "admin"

    );

    echo json_encode([

        'success'=>$ok

    ]);

break;

case 'DELETE':

    $ok=$repository->deleteJob(

        (int)$body['id']

    );

    Audit::info(

        "JOBS",

        "DELETE",

        "Trabajo ID ".$body['id']." eliminado",

        "admin"

    );

    echo json_encode([

        'success'=>$ok

    ]);

break;

default:

    http_response_code(405);

    echo json_encode([

        'success'=>false,

        'message'=>'Método no permitido'

    ]);

}