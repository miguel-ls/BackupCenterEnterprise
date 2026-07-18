<?php

namespace BackupCenter\Core;

class Logger
{
    private string $logPath;

    public function __construct(string $logPath)
    {
        date_default_timezone_set('America/Lima');

        $this->logPath = rtrim($logPath, DIRECTORY_SEPARATOR);

        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }
    }

    private function write(string $level, string $message): void
    {
        $file = $this->logPath . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log';

        $line = sprintf(
            "[%s] [%s] %s%s",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            PHP_EOL
        );

        file_put_contents($file, $line, FILE_APPEND);
    }

    public function info(string $message): void
    {
        $this->write('INFO', $message);
    }

    public function warning(string $message): void
    {
        $this->write('WARNING', $message);
    }

    public function error(string $message): void
    {
        $this->write('ERROR', $message);
    }

    public function success(string $message): void
    {
        $this->write('SUCCESS', $message);
    }
}