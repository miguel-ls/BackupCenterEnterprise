<?php

require_once __DIR__ . '/../vendor/autoload.php';

use BackupCenter\Core\Application;

$app = new Application();

echo PHP_EOL;
echo "==========================================" . PHP_EOL;
echo " Backup Center Scheduler Service" . PHP_EOL;
echo "==========================================" . PHP_EOL;

while (true) {

    echo PHP_EOL;
    echo "[" . date('Y-m-d H:i:s') . "] Revisando trabajos..." . PHP_EOL;

    $app
        ->schedulerService()
        ->run();

    sleep(10);
}