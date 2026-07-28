param(
    [string]$InstallDir = "$env:ProgramFiles\BackupCenter",
    [string]$ServiceName = "BackupCenterWorker"
)

$ErrorActionPreference = "Stop"

#------------------------------------------------------------
# Crear directorio de instalación
#------------------------------------------------------------

if (-not (Test-Path $InstallDir)) {
    New-Item -ItemType Directory -Path $InstallDir | Out-Null
}

$configPath = Join-Path $InstallDir "config.json"
$logPath = Join-Path $InstallDir "install.log"

$installerFolder = Split-Path -Parent $MyInvocation.MyCommand.Path
$agentSource = Join-Path $installerFolder "..\agent"
$packageConfigPath = Join-Path $installerFolder "..\config.json"

#------------------------------------------------------------
# Validar agente
#------------------------------------------------------------

if (-not (Test-Path $agentSource)) {
    throw "No se encontro la carpeta agent."
}

$agentExe = Join-Path $agentSource "BackupCenterAgent.exe"

if (-not (Test-Path $agentExe)) {
    throw "No se encontro BackupCenterAgent.exe en la carpeta agent."
}

#------------------------------------------------------------
# Detener servicio si existe
#------------------------------------------------------------

$service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue

if ($service -and $service.Status -eq "Running") {
    Stop-Service $ServiceName -Force
}

#------------------------------------------------------------
# Copiar archivos del agente
#------------------------------------------------------------

Copy-Item "$agentSource\*" $InstallDir -Recurse -Force

#------------------------------------------------------------
# Copiar configuración
#------------------------------------------------------------

if (-not (Test-Path $configPath)) {

    if (Test-Path $packageConfigPath) {

        Copy-Item $packageConfigPath $configPath

    }
    else {

@'
{
  "server": "https://backup.codesicorp.net",
  "clientId": 0,
  "connectionId": 0,
  "connectionName": "Nueva conexion",
  "installToken": ""
}
'@ | Set-Content -Path $configPath -Encoding utf8

    }

}

#------------------------------------------------------------
# Log instalación
#------------------------------------------------------------

$installLog = @"
[install]
InstallDir=$InstallDir
ServiceName=$ServiceName
ConfigPath=$configPath
Timestamp=$(Get-Date -Format o)
"@

Set-Content -Path $logPath -Value $installLog -Encoding utf8

#------------------------------------------------------------
# Registrar/Iniciar servicio
#------------------------------------------------------------

try {

    $exePath = Join-Path $InstallDir "BackupCenterAgent.exe"

    if (-not (Test-Path $exePath)) {
        throw "No se encontro BackupCenterAgent.exe"
    }

    $service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue

    if (-not $service) {

        New-Service `
            -Name $ServiceName `
            -BinaryPathName "`"$exePath`"" `
            -DisplayName "BackupCenter Agent" `
            -StartupType Automatic | Out-Null

    }

    $service = Get-Service -Name $ServiceName

    if ($service.Status -ne "Running") {
        Start-Service $ServiceName
    }

    $service = Get-Service -Name $ServiceName

}
catch {
    Write-Warning $_.Exception.Message
}

#------------------------------------------------------------
# Resultado
#------------------------------------------------------------

Write-Host ""
Write-Host "=============================================="
Write-Host " Instalacion completada"
Write-Host "=============================================="
Write-Host ""
Write-Host "Directorio : $InstallDir"
Write-Host "Configuracion : $configPath"
Write-Host "Log : $logPath"
Write-Host "Servicio : $ServiceName"
Write-Host "Estado : $($service.Status)"
Write-Host ""