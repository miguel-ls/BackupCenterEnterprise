<?php

require_once __DIR__ . '/vendor/autoload.php';

use BackupCenter\Console\ConsoleKernel;

echo PHP_EOL;
echo "============================================" . PHP_EOL;
echo " Backup Center Enterprise Console" . PHP_EOL;
echo "============================================" . PHP_EOL;
echo PHP_EOL;

exit(
    (new ConsoleKernel())->run($argv)
);