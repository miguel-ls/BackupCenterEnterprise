param(
    [string]$InstallToken
)

$ErrorActionPreference = "Stop"

Write-Host "DEBUG: SCRIPT INICIADO"
Write-Host "TOKEN:"
Write-Host $InstallToken

# VALIDAR TOKEN
$apiUrl = "https://backup.codesicorp.net/api/validate-token.php"

try {
    $body = @{
        InstallToken = $InstallToken
    } | ConvertTo-Json

    $response = Invoke-RestMethod -Uri $apiUrl -Method Post -Body $body -ContentType "application/json"

    if (-not $response.valid) {
        Write-Host "TOKEN INVALIDO"
        exit
    }

    Write-Host "TOKEN OK"

} catch {
    Write-Host "ERROR VALIDANDO TOKEN"
    exit
}

# MARCAR INSTALADO
$markUrl = "https://backup.codesicorp.net/api/mark-installed.php"

try {
    $body = @{
        InstallToken = $InstallToken
    } | ConvertTo-Json

    $response = Invoke-RestMethod -Uri $markUrl -Method Post -Body $body -ContentType "application/json"

    Write-Host "RESPUESTA:"
    $response | ConvertTo-Json

} catch {
    Write-Host "ERROR API"
}

Write-Host "FIN"