<?php

use BackupCenter\Core\ApiController;
use BackupCenter\Core\ApiResponse;

require_once __DIR__ . '/bootstrap.php';

ApiController::boot();
ApiController::method('GET');

function ps(string $command): string
{
    $powershell = 'C:\\Windows\\System32\\WindowsPowerShell\\v1.0\\powershell.exe';

    $cmd =
        '"' .
        $powershell .
        '" -NoProfile -ExecutionPolicy Bypass -Command "' .
        $command .
        '"';

    return trim(shell_exec($cmd) ?? '');
}

/*
|--------------------------------------------------------------------------
| CPU
|--------------------------------------------------------------------------
*/

$cpu = (int) ps(
    "(Get-CimInstance Win32_Processor).LoadPercentage"
);

/*
|--------------------------------------------------------------------------
| Memoria
|--------------------------------------------------------------------------
*/

$totalMemoryBytes = (float) ps(
    "(Get-CimInstance Win32_ComputerSystem).TotalPhysicalMemory"
);

$totalMemory = round(
    $totalMemoryBytes / 1024 / 1024 / 1024,
    1
);

$freeMemoryKb = (float) ps(
    "(Get-CimInstance Win32_OperatingSystem).FreePhysicalMemory"
);

$freeMemory = round(
    $freeMemoryKb / 1024 / 1024,
    1
);

$usedMemory = round(
    $totalMemory - $freeMemory,
    1
);

/*
|--------------------------------------------------------------------------
| Sistema Operativo
|--------------------------------------------------------------------------
*/

$os = ps("(Get-CimInstance Win32_OperatingSystem).Caption");

$version = ps("(Get-CimInstance Win32_OperatingSystem).Version");

/*
|--------------------------------------------------------------------------
| Uptime
|--------------------------------------------------------------------------
*/

$uptime = ps("
\$boot = (Get-CimInstance Win32_OperatingSystem).LastBootUpTime;
\$span = New-TimeSpan -Start \$boot -End (Get-Date);
\$span.Days.ToString() + '|' + \$span.Hours.ToString() + '|' + \$span.Minutes.ToString()
");

$parts = explode('|', trim($uptime));

if (count($parts) === 3) {

    $uptime = sprintf(
        '%d días %d horas %d min',
        (int)$parts[0],
        (int)$parts[1],
        (int)$parts[2]
    );

} else {

    $uptime = '-';

}

/*
|--------------------------------------------------------------------------
| Disco
|--------------------------------------------------------------------------
*/

$diskFree = round(
    disk_free_space(dirname(__DIR__,2))/1024/1024/1024,
    2
);

$diskTotal = round(
    disk_total_space(dirname(__DIR__,2))/1024/1024/1024,
    2
);

ApiResponse::success([

    "hostname"=>gethostname(),

    "os"=>$os,

    "os_version"=>$version,

    "php_version"=>PHP_VERSION,

    "time"=>date("Y-m-d H:i:s"),

    "cpu"=>round((float)$cpu,1),

    "memory_total"=>$totalMemory,

    "memory_used"=>$usedMemory,

    "memory_free"=>$freeMemory,

    "disk_total"=>$diskTotal,

    "disk_free"=>$diskFree,

    "uptime"=>$uptime

]);