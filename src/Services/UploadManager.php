<?php

namespace BackupCenter\Services;

use BackupCenter\Contracts\IConfiguration;
use BackupCenter\Builders\ScriptBuilder;
use BackupCenter\Core\WinScpProvider;
use BackupCenter\Models\BackupFile;
use BackupCenter\Core\RetryPolicy;

class UploadManager
{
    private IConfiguration $config;
    private WinScpProvider $provider;
    private RetryPolicy $retryPolicy;
    private ScriptBuilder $builder;

    public function __construct(
        IConfiguration $config,
        WinScpProvider $provider,
        ScriptBuilder $builder,
        RetryPolicy $retryPolicy
    ) {
        $this->config = $config;
        $this->provider = $provider;
        $this->builder = $builder;
        $this->retryPolicy = $retryPolicy;
    }

    public function upload(BackupFile $file): string
    {
        $connection = $this->config->getConnectionConfig();

        $lastException = null;

        for (
            $attempt = 1;
            $attempt <= $this->retryPolicy->getAttempts();
            $attempt++
        ) {

            try {

                $script = $this->builder
                    ->batchAbort()
                    ->confirmOff()
                    ->open($connection)
->put($file->getPath())
                    ->exit()
                    ->build();

                return $this->provider->execute($script);

            } catch (\Throwable $e) {

                $lastException = $e;

                echo "Intento {$attempt} falló: {$e->getMessage()}" . PHP_EOL;

                if (!$this->retryPolicy->shouldRetry($attempt)) {
                    break;
                }

                echo "Reintentando en "
                    . $this->retryPolicy->getDelay()
                    . " segundos..."
                    . PHP_EOL;

                sleep($this->retryPolicy->getDelay());
            }
        }

        throw $lastException;
    }
}