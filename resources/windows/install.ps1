param(
    [string]$InstallDir = "$env:ProgramFiles\BackupCenter",
    [string]$ServiceName = "BackupCenterAgent"
)

$ErrorActionPreference = "Stop"

#------------------------------------------------------------
# Rutas
#------------------------------------------------------------

$AgentDir    = Join-Path $InstallDir "agent"
$AgentExe    = Join-Path $AgentDir "BackupCenterAgent.exe"
$ConfigPath  = Join-Path $AgentDir "config.json"
$LogPath     = Join-Path $InstallDir "install.log"

#------------------------------------------------------------
# Validaciones
#------------------------------------------------------------

if (-not (Test-Path $InstallDir)) {
    throw "No existe el directorio de instalación: $InstallDir"
}

if (-not (Test-Path $AgentDir)) {
    throw "No existe la carpeta del Agent: $AgentDir"
}

if (-not (Test-Path $AgentExe)) {
    throw "No se encontró BackupCenterAgent.exe"
}

#------------------------------------------------------------
# Crear config.json si no existe
#------------------------------------------------------------

if (-not (Test-Path $ConfigPath)) {

@'
{
  "Server": "https://backup.codesicorp.net",
  "InstallToken": "",

  "Backup": {
    "Extensions": [
      "zip",
      "rar",
      "bak"
    ]
  }
}
'@ | Set-Content -Encoding UTF8 $ConfigPath

}

#------------------------------------------------------------
# Detener servicio existente
#------------------------------------------------------------

$service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue

if ($service) {

    if ($service.Status -eq "Running") {
        Stop-Service $ServiceName -Force
    }

    sc.exe delete $ServiceName | Out-Null

    Start-Sleep -Seconds 2
}

#------------------------------------------------------------
# Registrar servicio
#------------------------------------------------------------

New-Service `
    -Name $ServiceName `
    -BinaryPathName "`"$AgentExe`"" `
    -DisplayName "Backup Center Agent" `
    -Description "Backup Center Agent Service" `
    -StartupType Automatic

#------------------------------------------------------------
# Iniciar servicio
#------------------------------------------------------------

Start-Service $ServiceName

Start-Sleep -Seconds 2

$service = Get-Service $ServiceName

#------------------------------------------------------------
# Log
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
Write-Host " Backup Center Agent instalado"
Write-Host "========================================="
Write-Host ""
Write-Host "Servicio : $ServiceName"
Write-Host "Estado   : $($service.Status)"
Write-Host "Ruta     : $AgentExe"
Write-Host "Config   : $ConfigPath"
Write-Host ""