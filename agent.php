<?php

require_once __DIR__ . '/vendor/autoload.php';

use BackupCenter\Core\Application;

$app = new Application();

$app->agent()->run();