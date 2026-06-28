<?php

namespace BackupCenter\Core;

class WinScpProvider
{
    private string $winScp;

    public function __construct(
        string $winScp = 'C:\Program Files (x86)\WinSCP\WinSCP.com'
    ) {
        $this->winScp = $winScp;
    }

    public function execute(string $script): string
    {
        $tempFile = __DIR__ . '/../../storage/temp/winscp_' . uniqid() . '.txt';

        file_put_contents($tempFile, $script);

        $command = sprintf(
            '"%s" /script="%s"',
            $this->winScp,
            $tempFile
        );

        $output = shell_exec($command);

        unlink($tempFile);

        return $output ?? '';
    }
}