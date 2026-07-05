<?php

namespace BackupCenter\Services;

use BackupCenter\Contracts\IConfiguration;
use BackupCenter\Builders\ScriptBuilder;
use BackupCenter\Core\RetryPolicy;
use BackupCenter\Core\WinScpProvider;

class UploadManagerFactory
{
    public function create(
        IConfiguration $configuration,
        RetryPolicy $retryPolicy
    ): UploadManager {

        return new UploadManager(
            $configuration,
            new WinScpProvider(),
            new ScriptBuilder(),
            $retryPolicy
        );
    }
}