<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/bootstrap.php';

use BackupCenter\Core\Application;
use BackupCenter\Core\Paths;
use BackupCenter\Repositories\ExecutionHistoryRepository;
use BackupCenter\Services\WindowsServiceMonitor;

$app = new Application();

$monitor = new WindowsServiceMonitor();

$history = new ExecutionHistoryRepository(
    $app->database()
);

$db = Paths::database() . '/backupcenter.db';

$internet = @fsockopen("8.8.8.8",53,$errno,$errstr,2);

$data=[

    "status"=>[

        [

            "name"=>"Scheduler",

            "ok"=>$monitor->getStatus("BackupCenterScheduler")=="Activo"

        ],

        [

            "name"=>"Worker",

            "ok"=>$monitor->getStatus("BackupCenterWorker")=="Activo"

        ],

        [

            "name"=>"SQLite",

            "ok"=>file_exists($db)

        ],

        [

            "name"=>"Internet",

            "ok"=>$internet!==false

        ]

    ],

    "events"=>[],

    "alerts"=>[]

];

if($internet){
    fclose($internet);
}

$last=$history->latest(5);

foreach($last as $row){

    $data["events"][]=[

        "title"=>"Backup ".$row["client"],

        "status"=>$row["status"],

        "date"=>$row["executed_at"]

    ];

}

if($monitor->getStatus("BackupCenterScheduler")!="Activo"){

    $data["alerts"][]="Scheduler detenido";

}

if($monitor->getStatus("BackupCenterWorker")!="Activo"){

    $data["alerts"][]="Worker detenido";

}

echo json_encode([

    "success"=>true,

    "data"=>$data

]);