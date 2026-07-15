<?php

namespace BackupCenter\Services;

class WindowsServiceMonitor
{
    private string $powershell;

    public function __construct()
    {
        $this->powershell =
            'C:\\Windows\\System32\\WindowsPowerShell\\v1.0\\powershell.exe';
    }

    private function execute(string $script): string
    {
        $command =
            '"' .
            $this->powershell .
            '" -NoProfile -ExecutionPolicy Bypass -Command "' .
            $script .
            '"';

        return trim(shell_exec($command) ?? '');
    }

    public function getStatus(string $service): string
    {
        $status = strtoupper(
            $this->execute(
                "(Get-Service -Name '$service').Status"
            )
        );

        return match ($status) {

            'RUNNING' => 'Activo',

            'STOPPED' => 'Inactivo',

            'PAUSED' => 'Pausado',

            default => 'Desconocido'

        };
    }

    public function exists(string $service): bool
    {
        return strtoupper(
            $this->execute(
                "(Get-Service -Name '$service' -ErrorAction SilentlyContinue) -ne \$null"
            )
        ) === "TRUE";
    }

    public function getPid(string $service): ?int
    {
        $pid = $this->execute(
            "(Get-CimInstance Win32_Service -Filter \"Name='$service'\").ProcessId"
        );

        return is_numeric($pid)
            ? (int)$pid
            : null;
    }

    public function getMemory(int $pid): string
    {
        if ($pid <= 0) {
            return "-";
        }

        $memory = $this->execute(
            "(Get-Process -Id $pid).WorkingSet64/1MB"
        );

        if (!is_numeric($memory)) {
            return "-";
        }

        return round((float)$memory,2)." MB";
    }

    public function getCpu(int $pid): string
    {
        if ($pid <= 0) {
            return "-";
        }

        $cpu = $this->execute(
            "(Get-Process -Id $pid).CPU"
        );

        return $cpu === ""
            ? "-"
            : round((float)$cpu,2)." s";
    }

    public function getStartTime(int $pid): string
    {
        if ($pid <= 0) {
            return "-";
        }

        return $this->execute(
            "(Get-Process -Id $pid).StartTime.ToString('yyyy-MM-dd HH:mm:ss')"
        );
    }

    public function getServiceInfo(string $service): array
    {
        $pid = $this->getPid($service);

        return [

            "name"=>$service,

            "exists"=>$this->exists($service),

            "status"=>$this->getStatus($service),

            "pid"=>$pid,

            "memory"=>$this->getMemory($pid ?? 0),

            "cpu"=>$this->getCpu($pid ?? 0),

            "started_at"=>$this->getStartTime($pid ?? 0)

        ];
    }
}