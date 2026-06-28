<?php

require_once __DIR__ . '/../vendor/autoload.php';

use BackupCenter\Core\ScriptBuilder;

$script = (new ScriptBuilder())
    ->batchAbort()
    ->confirmOff()
    ->open('CODESICORP')
    ->put(
        'B:\\Backup ERP\\ERP.zip',
        '/backups/unimarket/'
    )
    ->exit()
    ->build();

echo $script;