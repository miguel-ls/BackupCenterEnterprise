<?php

require_once __DIR__ . "/bootstrap.php";

use BackupCenter\Repositories\JobQueueRepository;

/*
|--------------------------------------------------------------------------
| CORS
|--------------------------------------------------------------------------
*/

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: *");

/*
|--------------------------------------------------------------------------
| SSE
|--------------------------------------------------------------------------
*/

header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");

$queue = new JobQueueRepository($db);

while (true) {

    $status = [

        "time" => date("Y-m-d H:i:s"),

        "memory" => round(
            memory_get_usage(true) / 1024 / 1024,
            2
        ),

        "queue" => $queue->countPending(),

        "running" => $queue->countRunning(),

        "failed" => $queue->countFailed()

    ];

    echo "data: " . json_encode($status) . "\n\n";

    @ob_flush();

    @flush();

    sleep(1);

}