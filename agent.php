<?php

require_once __DIR__ . '/vendor/autoload.php';

use BackupCenter\Core\BackupCenterAgent;
use BackupCenter\Core\ConfigurationManager;
use BackupCenter\Core\FileScanner;
use BackupCenter\Core\FileValidator;
use BackupCenter\Core\Logger;
use BackupCenter\Core\WinScpProvider;
use BackupCenter\Services\UploadManager;

$config = new ConfigurationManager(
    __DIR__ . '/resources/config/config.json'
);

$logger = new Logger(
    __DIR__ . '/storage/logs'
);

$scanner = new FileScanner();

$validator = new FileValidator();

$provider = new WinScpProvider();

$uploadManager = new UploadManager(
    $config,
    $provider
);

$agent = new BackupCenterAgent(
    $config,
    $logger,
    $scanner,
    $validator,
    $provider,
    $uploadManager
);

$agent->run();