param(
    [string]$InstallDir = "$env:ProgramFiles\BackupCenter",
    [string]$ServiceName = "BackupCenterAgent"
)

$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "========================================="
Write-Host " Backup Center Enterprise Agent Installer"
Write-Host "========================================="
Write-Host ""

#------------------------------------------------------------
# Rutas
#------------------------------------------------------------

$SourceDir    = Split-Path -Parent $PSScriptRoot
$SourceAgent  = Join-Path $SourceDir "agent"

$AgentDir     = Join-Path $InstallDir "agent"
$AgentExe     = Join-Path $AgentDir "BackupCenterAgent.exe"

$SourceConfig = Join-Path $SourceDir "config.json"
$ConfigPath   = Join-Path $AgentDir "config.json"

$LogPath      = Join-Path $InstallDir "install.log"

#------------------------------------------------------------
# Validaciones
#------------------------------------------------------------

if (-not (Test-Path $SourceAgent)) {
    throw "No se encontró la carpeta 'agent'."
}

if (-not (Test-Path (Join-Path $SourceAgent "BackupCenterAgent.exe"))) {
    throw "No se encontró BackupCenterAgent.exe."
}

#------------------------------------------------------------
# Crear directorios
#------------------------------------------------------------

Write-Host "Creando directorios..."

New-Item -ItemType Directory -Force -Path $InstallDir | Out-Null
New-Item -ItemType Directory -Force -Path $AgentDir | Out-Null

#------------------------------------------------------------
# ¿Existe el servicio?
#------------------------------------------------------------

$service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue

if ($service) {

    Write-Host "Actualizando instalación existente..."

    if ($service.Status -eq "Running") {
        Stop-Service $ServiceName -Force
    }
}

#------------------------------------------------------------
# Copiar archivos
#------------------------------------------------------------

Write-Host "Copiando archivos..."

Copy-Item `
    -Path (Join-Path $SourceAgent "*") `
    -Destination $AgentDir `
    -Recurse `
    -Force

if (Test-Path $SourceConfig) {

    Copy-Item `
        $SourceConfig `
        $ConfigPath `
        -Force
}

#------------------------------------------------------------
# Crear servicio si no existe
#------------------------------------------------------------

if (-not $service) {

    Write-Host "Creando servicio..."

    New-Service `
        -Name $ServiceName `
        -BinaryPathName "`"$AgentExe`"" `
        -DisplayName "Backup Center Agent" `
        -Description "Backup Center Agent Service" `
        -StartupType Automatic
}

#------------------------------------------------------------
# Iniciar servicio
#------------------------------------------------------------

Write-Host "Iniciando servicio..."

Start-Service $ServiceName

Start-Sleep -Seconds 2

$service = Get-Service $ServiceName

#------------------------------------------------------------
# Crear log
#------------------------------------------------------------

@"
==================================================
Backup Center Agent
==================================================

Fecha........: $(Get-Date)

Directorio...: $InstallDir

Agent........: $AgentExe

Servicio.....: $ServiceName

Estado.......: $($service.Status)

Config.......: $ConfigPath

==================================================
"@ | Set-Content $LogPath -Encoding UTF8

#------------------------------------------------------------
# Resultado
#------------------------------------------------------------

Write-Host ""
Write-Host "========================================="
Write-Host " Instalación completada"
Write-Host "========================================="
Write-Host ""

Write-Host "Servicio : $ServiceName"
Write-Host "Estado   : $($service.Status)"
Write-Host "Ruta     : $AgentExe"
Write-Host "Config   : $ConfigPath"
Write-Host ""