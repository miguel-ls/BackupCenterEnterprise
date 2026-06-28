<?php

require_once __DIR__ . '/../vendor/autoload.php';

use BackupCenter\Core\ScriptBuilder;
use BackupCenter\Core\WinScpProvider;
use BackupCenter\Core\ConfigurationManager;

$localFile = realpath(__DIR__ . '/data/prueba.txt');

$config = new ConfigurationManager(
    __DIR__ . '/../resources/config/config.json'
);

$connection = $config->getConnectionConfig();

$script = (new ScriptBuilder())
    ->batchAbort()
    ->confirmOff()
    ->open($connection)
    ->build();

$script .= PHP_EOL . 'put "' . $localFile . '"';
$script .= PHP_EOL . 'exit';

echo "========== SCRIPT ==========" . PHP_EOL;
echo $script . PHP_EOL;
echo "============================" . PHP_EOL . PHP_EOL;

$provider = new WinScpProvider();

$result = $provider->execute($script);

echo $result;