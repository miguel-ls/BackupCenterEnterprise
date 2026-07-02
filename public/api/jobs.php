<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');

require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Database;
use BackupCenter\Core\Paths;
use BackupCenter\Repositories\JobRepository;

$db = new Database(
    Paths::database() . '/backupcenter.db'
);

$repository = new JobRepository($db);

echo json_encode(
    $repository->getJobs()
);