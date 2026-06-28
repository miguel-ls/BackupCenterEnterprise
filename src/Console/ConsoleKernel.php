<?php

namespace BackupCenter\Console;

use BackupCenter\Console\Commands\DoctorCommand;
use BackupCenter\Console\Commands\InfoCommand;
use BackupCenter\Console\Commands\StatusCommand;
use BackupCenter\Console\Commands\VersionCommand;
use BackupCenter\Console\Commands\HistoryCommand;
use BackupCenter\Core\Application;
use BackupCenter\Console\Commands\StatsCommand;
use BackupCenter\Console\Commands\LogsCommand;

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

            'history' => (new HistoryCommand())->execute(
                new Application()
            ),            

            'stats' => (new StatsCommand())->execute(
                new Application()
            ),            

            'logs' => (new LogsCommand())->execute(),

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
        echo "history" . PHP_EOL;
        echo "stats" . PHP_EOL;
        echo "logs" . PHP_EOL;

        return 0;
    }

    private function unknown(string $command): int
    {
        echo "Comando desconocido: {$command}" . PHP_EOL;
        echo "Ejecute: php console.php help" . PHP_EOL;

        return 1;
    }
}