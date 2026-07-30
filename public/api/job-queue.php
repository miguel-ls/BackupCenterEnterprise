<?php

require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use BackupCenter\Core\Application;
use BackupCenter\Repositories\JobQueueRepository;

$app = new Application();

$repository = new JobQueueRepository(
    $app->database()
);

$repository->initialize();

echo json_encode([

    "success" => true,

    "data" => $repository->getAll()

]);