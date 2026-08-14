# ============================================================
# BackupCenter Agent Installer (FINAL CON VALIDACIÓN TOKEN)
# ============================================================

param(
    [string]$InstallToken
)

$ErrorActionPreference = "Stop"

$ServiceName = "BackupCenterAgent"
$InstallDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$AgentDir = Join-Path $InstallDir "agent"
$AgentExe = Join-Path $AgentDir "BackupCenterAgent.exe"

# Log
$LogPath = Join-Path $InstallDir "install.log"
Start-Transcript -Path $LogPath -Append

Write-Host "==== Instalando BackupCenter Agent ===="

# ============================================================
# VALIDAR TOKEN CONTRA API
# ============================================================

Write-Host "Validando token contra el servidor..."

$apiUrl = "https://backup.codesicorp.net/api/validate-token.php"

try {
    $body = @{
        InstallToken = $InstallToken
    } | ConvertTo-Json

    $response = Invoke-RestMethod `
        -Uri $apiUrl `
        -Method Post `
        -Body $body `
        -ContentType "application/json"

    if (-not $response.valid) {
        Write-Host "ERROR: Token inválido"
        Stop-Transcript
        exit 1
    }

    Write-Host "Token válido ✔"

} catch {
    Write-Host "ERROR: No se pudo validar el token"
    Stop-Transcript
    exit 1
}

# ============================================================
# GENERAR CONFIG DINÁMICO
# ============================================================

$ConfigPath = Join-Path $AgentDir "config.json"

$configData = @{
    Server = "https://backup.codesicorp.net"
    InstallToken = $InstallToken
    Backup = @{
        Extensions = @("zip", "rar", "bak")
    }
}

$configJson = $configData | ConvertTo-Json -Depth 5

$configJson | Out-File -Encoding UTF8 -FilePath $ConfigPath

Write-Host "Config generado en: $ConfigPath"

# ============================================================
# VALIDAR EXE
# ============================================================

if (!(Test-Path $AgentExe)) {
    Write-Error "No se encontró el ejecutable del agente: $AgentExe"
    Stop-Transcript
    exit 1
}

# ============================================================
# SERVICIO
# ============================================================

$service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue

if ($service) {
    Write-Host "Servicio existe, actualizando..."

    if ($service.Status -ne "Stopped") {
        Stop-Service $ServiceName -Force
        Start-Sleep -Seconds 2
    }

    sc.exe delete $ServiceName | Out-Null
    Start-Sleep -Seconds 2
}

Write-Host "Creando servicio..."

New-Service `
    -Name $ServiceName `
    -BinaryPathName "`"$AgentExe`"" `
    -DisplayName "Backup Center Agent" `
    -StartupType Automatic

sc.exe description $ServiceName "Backup Center Agent Service" | Out-Null

Start-Service $ServiceName

Write-Host "Servicio instalado y ejecutándose correctamente"

Stop-Transcript
exit 0