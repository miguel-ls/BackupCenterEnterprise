<?php

require_once __DIR__ . '/cors.php';

echo json_encode([
    "success" => true,
    "data" => [
        [
            "job_id" => 1,
            "file_name" => "backup1.zip",
            "client_name" => "UNIMARKET",
            "job_name" => "Job Unimarket",
            "uploaded_bytes" => 52428800,
            "total_bytes" => 104857600,
            "speed" => 5.2
        ],
        [
            "job_id" => 2,
            "file_name" => "backup2.zip",
            "client_name" => "CODESICORP",
            "job_name" => "Job Codesicorp",
            "uploaded_bytes" => 20971520,
            "total_bytes" => 104857600,
            "speed" => 3.1
        ]
    ]
]);
