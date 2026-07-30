param(
    [string]$InstallDir = "$env:ProgramFiles\BackupCenter",
    [string]$ServiceName = "BackupCenterAgent"
)

$ErrorActionPreference = "Stop"

Write-Host ""
Write-Host "========================================="
Write-Host " Backup Center Enterprise Agent"
Write-Host " Desinstalador"
Write-Host "========================================="
Write-Host ""

$AgentDir = Join-Path $InstallDir "agent"

#------------------------------------------------------------
# Detener servicio
#------------------------------------------------------------

$service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue

if ($service) {

    if ($service.Status -eq "Running") {

        Write-Host "Deteniendo servicio..."

        Stop-Service $ServiceName -Force

        Start-Sleep -Seconds 2
    }

    Write-Host "Eliminando servicio..."

    sc.exe delete $ServiceName | Out-Null

    Start-Sleep -Seconds 2
}
else {

    Write-Host "El servicio no existe."
}

#------------------------------------------------------------
# Eliminar archivos
#------------------------------------------------------------

if (Test-Path $InstallDir) {

    Write-Host "Eliminando archivos..."

    Remove-Item `
        -Path $InstallDir `
        -Recurse `
        -Force
}

Write-Host ""
Write-Host "========================================="
Write-Host " Desinstalación completada"
Write-Host "========================================="
Write-Host ""

pause