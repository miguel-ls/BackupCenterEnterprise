<?php

namespace BackupCenter\Services;

use BackupCenter\Core\ConfigurationManager;
use BackupCenter\Core\ScriptBuilder;
use BackupCenter\Core\WinScpProvider;
use BackupCenter\Models\BackupFile;

class UploadManager
{
    private ConfigurationManager $config;
    private WinScpProvider $provider;

    public function __construct(
        ConfigurationManager $config,
        WinScpProvider $provider
    ) {
        $this->config = $config;
        $this->provider = $provider;
    }

    public function upload(BackupFile $file): string
    {
        $connection = $this->config->getConnectionConfig();

        $script = (new ScriptBuilder())
            ->batchAbort()
            ->confirmOff()
            ->open($connection)
            ->put($file->getPath())
            ->exit()
            ->build();

        return $this->provider->execute($script);
    }
}