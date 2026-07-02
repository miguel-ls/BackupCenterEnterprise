<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');

echo json_encode([
    [
        "id"=>1,
        "name"=>"ERP SQL",
        "status"=>"Correcto",
        "time"=>"07:30"
    ],
    [
        "id"=>2,
        "name"=>"NAS Principal",
        "status"=>"Correcto",
        "time"=>"06:45"
    ],
    [
        "id"=>3,
        "name"=>"Documentos",
        "status"=>"Error",
        "time"=>"06:10"
    ]
]);