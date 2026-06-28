<?php

namespace BackupCenter\Console\Commands;

use BackupCenter\Core\Application;

class ConfigCommand
{
    public function execute(Application $app): int
    {
        $config = $app->config();

        echo PHP_EOL;
        echo "Configuración actual" . PHP_EOL;
        echo "----------------------------------------" . PHP_EOL;

        echo "Cliente          : " . $config->get('client.name') . PHP_EOL;
        echo "Ruta backups     : " . $config->get('backup.local_path') . PHP_EOL;
        echo "Extensiones      : " . implode(', ', $config->get('backup.extensions', [])) . PHP_EOL;

        $connection = $config->getConnectionConfig();

        echo PHP_EOL;
        echo "SFTP" . PHP_EOL;
        echo "----------------------------------------" . PHP_EOL;

        echo "Host             : " . $connection->getHost() . PHP_EOL;
        echo "Puerto           : " . $connection->getPort() . PHP_EOL;
        echo "Usuario          : " . $connection->getUsername() . PHP_EOL;

        echo PHP_EOL;
        echo "Retry" . PHP_EOL;
        echo "----------------------------------------" . PHP_EOL;

        echo "Intentos         : " . $config->get('sftp.retry_attempts') . PHP_EOL;
        echo "Espera           : " . $config->get('sftp.retry_delay') . " segundos" . PHP_EOL;

        return 0;
    }
}