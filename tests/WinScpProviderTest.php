<?php

require_once __DIR__ . '/../vendor/autoload.php';

use BackupCenter\Core\ScriptBuilder;
use BackupCenter\Core\WinScpProvider;

$script = (new ScriptBuilder())
    ->batchAbort()
    ->confirmOff()
    ->open('CODESICORP')
    ->exit()
    ->build();

$provider = new WinScpProvider();

echo $provider->execute($script);