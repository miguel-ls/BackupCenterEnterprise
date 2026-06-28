<?php

namespace BackupCenter\Console;

use BackupCenter\Console\Commands\DoctorCommand;
use BackupCenter\Console\Commands\InfoCommand;
use BackupCenter\Console\Commands\StatusCommand;
use BackupCenter\Console\Commands\VersionCommand;

class ConsoleKernel
{
    public function run(array $argv): int
    {
        $command = strtolower($argv[1] ?? 'help');

        return match ($command) {

            'version' => (new VersionCommand())->execute(),

            'info' => (new InfoCommand())->execute(),

            'status' => (new StatusCommand())->execute(),

            'doctor' => (new DoctorCommand())->execute(),

            'help' => $this->help(),

            default => $this->unknown($command),
        };
    }

    private function help(): int
    {
        echo "Comandos disponibles" . PHP_EOL;
        echo "------------------------------" . PHP_EOL;
        echo "version" . PHP_EOL;
        echo "info" . PHP_EOL;
        echo "status" . PHP_EOL;
        echo "doctor" . PHP_EOL;

        return 0;
    }

    private function unknown(string $command): int
    {
        echo "Comando desconocido: {$command}" . PHP_EOL;
        echo "Ejecute: php console.php help" . PHP_EOL;

        return 1;
    }
}