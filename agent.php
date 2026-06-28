<?php

require_once __DIR__ . '/vendor/autoload.php';

use BackupCenter\Core\BackupCenterAgent;
use BackupCenter\Core\ConfigurationManager;
use BackupCenter\Core\FileScanner;
use BackupCenter\Core\FileValidator;
use BackupCenter\Core\Logger;
use BackupCenter\Core\WinScpProvider;
use BackupCenter\Services\UploadManager;
use BackupCenter\Core\Paths;

$config = new ConfigurationManager(
    Paths::config() . '/config.json'
);

$logger = new Logger(
    Paths::logs()
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