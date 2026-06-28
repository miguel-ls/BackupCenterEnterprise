<?php

require_once __DIR__ . '/vendor/autoload.php';
use BackupCenter\Core\Paths;

echo PHP_EOL;
echo "============================================" . PHP_EOL;
echo " Backup Center Enterprise Console" . PHP_EOL;
echo "============================================" . PHP_EOL;
echo PHP_EOL;

$command = $argv[1] ?? 'help';

switch ($command) {

    case 'version':
        echo "Version: 0.3.0-dev" . PHP_EOL;
        break;

    case 'info':

        $config = new BackupCenter\Core\ConfigurationManager(
            BackupCenter\Core\Paths::config() . '/config.json'
        );

        echo "Información del proyecto" . PHP_EOL;
        echo "------------------------------" . PHP_EOL;

        echo "Versión        : " .
            trim(file_get_contents(__DIR__ . '/VERSION'))
            . PHP_EOL;

        echo "Cliente        : " .
            $config->get('client.name')
            . PHP_EOL;

        echo "PHP            : " .
            PHP_VERSION
            . PHP_EOL;

        echo "Ruta backups   : " .
            $config->get('backup.local_path')
            . PHP_EOL;

        echo "Extensiones    : " .
            implode(', ', $config->get('backup.extensions', []))
            . PHP_EOL;

        echo "Logs           : " .
            BackupCenter\Core\Paths::logs()
            . PHP_EOL;

        echo "Base de datos  : " .
            BackupCenter\Core\Paths::database() . '/backupcenter.db'
            . PHP_EOL;

        break;        
    case 'help':
        echo "  php console.php info" . PHP_EOL;
        
    case 'doctor':

        echo "Diagnóstico del sistema" . PHP_EOL;
        echo "------------------------------" . PHP_EOL;

        $checks = [];

        $checks[] = [
            'PHP',
            version_compare(PHP_VERSION, '8.2.0', '>='),
            PHP_VERSION
        ];

        $checks[] = [
            'SQLite',
            extension_loaded('pdo_sqlite'),
            ''
        ];

        $checks[] = [
            'WinSCP',
            file_exists("C:\\Program Files (x86)\\WinSCP\\WinSCP.com"),
            ''
        ];

        $checks[] = [
            'Config',
            file_exists(Paths::config() . "/config.json"),
            ''
        ];

        $checks[] = [
            'Database',
            file_exists(Paths::database() . "/backupcenter.db"),
            ''
        ];

        $checks[] = [
            'Logs',
            is_writable(Paths::logs()),
            ''
        ];

        $checks[] = [
            'Temp',
            is_writable(Paths::temp()),
            ''
        ];

        foreach ($checks as $check) {

            echo sprintf(
                "[%s] %-12s %s",
                $check[1] ? "OK" : "ERROR",
                $check[0],
                $check[2]
            ) . PHP_EOL;
        }

        break;        
    case 'status':

        echo "Estado del sistema" . PHP_EOL;
        echo "------------------------------" . PHP_EOL;

        echo "PHP          : " . PHP_VERSION . PHP_EOL;

        echo "SQLite       : " .
            (extension_loaded('pdo_sqlite') ? "OK" : "ERROR")
            . PHP_EOL;

        echo "WinSCP       : " .
            (file_exists("C:\\Program Files (x86)\\WinSCP\\WinSCP.com") ? "OK" : "ERROR")
            . PHP_EOL;

        echo "Config       : " .
            (file_exists(__DIR__ . "/resources/config/config.json") ? "OK" : "ERROR")
            . PHP_EOL;

        echo "Database     : " .
            (file_exists(__DIR__ . "/storage/database/backupcenter.db") ? "OK" : "ERROR")
            . PHP_EOL;

        echo "Logs         : " .
            (is_dir(__DIR__ . "/storage/logs") ? "OK" : "ERROR")
            . PHP_EOL;

        break;        
    default:

        echo "Comandos disponibles:" . PHP_EOL;
        echo PHP_EOL;
        echo "  php console.php version" . PHP_EOL;
        echo "  php console.php status" . PHP_EOL;
        echo "  php console.php install" . PHP_EOL;
        echo "  php console.php run" . PHP_EOL;
        echo "  php console.php test-db" . PHP_EOL;
        echo "  php console.php test-sftp" . PHP_EOL;

        break;
}