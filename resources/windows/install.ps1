param(
    [string]$InstallDir = "$env:ProgramFiles\BackupCenter",
    [string]$ServiceName = 'BackupCenterWorker'
)

$ErrorActionPreference = 'Stop'

New-Item -ItemType Directory -Force -Path $InstallDir | Out-Null

$configPath = Join-Path $InstallDir 'config.json'
$logPath = Join-Path $InstallDir 'install.log'
$serviceScript = Join-Path $InstallDir 'start-service.cmd'
$workerLog = Join-Path $InstallDir 'worker.log'
$checkConfigScript = Join-Path $InstallDir 'check-config.php'
$packageConfigPath = Join-Path $InstallDir 'package-config.json'

if (Test-Path $packageConfigPath) {
    Copy-Item $packageConfigPath $configPath -Force
} elseif (-not (Test-Path $configPath)) {
    @'
{
  "server": "http://localhost:8000",
  "clientId": 0,
  "connectionId": 0,
  "connectionName": "Nueva conexión",
  "installToken": ""
}
'@ | Set-Content -Path $configPath -Encoding utf8
}

@"
@echo off
setlocal
set LOGFILE=%~dp0worker.log
if not exist "%~dp0..\..\public\worker.php" (
    echo [%DATE% %TIME%] worker.php no encontrado >> "%LOGFILE%"
    exit /b 1
)

echo [%DATE% %TIME%] iniciando worker >> "%LOGFILE%"
php "%~dp0..\..\public\worker.php" >> "%LOGFILE%" 2>&1
"@ | Set-Content -Path $serviceScript -Encoding ascii

$installLog = @"
[install]
InstallDir=$InstallDir
ServiceName=$ServiceName
ConfigPath=$configPath
Timestamp=$(Get-Date -Format o)
"@

Set-Content -Path $logPath -Value $installLog -Encoding utf8

try {
    $service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
    if (-not $service) {
        New-Service -Name $ServiceName -BinaryPathName "cmd.exe /c `"$serviceScript`"" -DisplayName 'BackupCenter Worker' -StartupType Automatic | Out-Null
    }
} catch {
    Write-Warning "No se pudo registrar el servicio automáticamente: $($_.Exception.Message)"
}

if (Test-Path $checkConfigScript) {
    & php $checkConfigScript | Out-File -FilePath $workerLog -Encoding utf8
}

Write-Host "Instalación preparada en $InstallDir"
Write-Host "Archivo de configuración: $configPath"
Write-Host "Log de instalación: $logPath"
Write-Host "Prueba de configuración guardada en $workerLog"
