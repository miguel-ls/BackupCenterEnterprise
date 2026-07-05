<?php

require_once __DIR__ . '/../vendor/autoload.php';

use BackupCenter\Core\Application;

echo PHP_EOL;
echo "==========================================" . PHP_EOL;
echo " Backup Center Scheduler" . PHP_EOL;
echo "==========================================" . PHP_EOL;

$app = new Application();

$app
    ->schedulerService()
    ->run();

echo PHP_EOL;
echo "Scheduler finalizado." . PHP_EOL;