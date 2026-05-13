#Requires -RunAsAdministrator
<#
.SYNOPSIS
  Corrige el arranque del servicio PostgreSQL 18 en Windows (error 1067) en muchos equipos.

.DESCRIPTION
  - Detiene el servicio postgresql-x64-18.
  - Cambia la cuenta de inicio a "Sistema local" (NT AUTHORITY\LocalSystem), que suele
    evitar fallos de permisos con Network Service.
  - Concede permisos completos en la carpeta data a SYSTEM y Administradores (idempotente).
  - Elimina postmaster.pid si quedó huérfano (solo con servicio detenido).
  - Inicia el servicio y muestra el estado.

  Si prefieres seguir con Docker para desarrollo, no necesitas este script.

.NOTES
  Ejecutar: clic derecho en PowerShell -> "Ejecutar como administrador", luego:
    Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass -Force
    & "C:\ruta\al\repo\scripts\fix-postgresql-18-windows-service.ps1"
#>

$ErrorActionPreference = "Stop"

$serviceName = "postgresql-x64-18"
$dataDir     = "C:\Program Files\PostgreSQL\18\data"
$pidFile     = Join-Path $dataDir "postmaster.pid"

Write-Host "=== PostgreSQL 18 - reparar servicio Windows ===" -ForegroundColor Cyan

$svc = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
if (-not $svc) {
    Write-Error "No se encontro el servicio '$serviceName'. Ajusta `$serviceName en el script si tu version es otra."
    exit 1
}

if ($svc.Status -eq "Running") {
    Write-Host "Deteniendo servicio..." -ForegroundColor Yellow
    Stop-Service -Name $serviceName -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 2
}

if (Test-Path $pidFile) {
    Write-Host "Eliminando postmaster.pid huérfano..." -ForegroundColor Yellow
    Remove-Item $pidFile -Force -ErrorAction SilentlyContinue
}

if (Test-Path $dataDir) {
    Write-Host "Ajustando permisos en: $dataDir" -ForegroundColor Yellow
    & icacls $dataDir /grant "NT AUTHORITY\SYSTEM:(OI)(CI)F" /T | Out-Null
    & icacls $dataDir /grant "BUILTIN\Administradores:(OI)(CI)F" /T | Out-Null
    & icacls $dataDir /grant "NT AUTHORITY\NETWORK SERVICE:(OI)(CI)F" /T | Out-Null
}

Write-Host "Cambiando cuenta del servicio a Sistema local (LocalSystem)..." -ForegroundColor Yellow
& sc.exe config $serviceName obj= "NT AUTHORITY\LocalSystem" | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Error "sc config fallo (codigo $LASTEXITCODE). ¿Ejecutaste PowerShell como administrador?"
    exit $LASTEXITCODE
}

Write-Host "Iniciando servicio..." -ForegroundColor Yellow
Start-Service -Name $serviceName
Start-Sleep -Seconds 2

Get-Service -Name $serviceName | Format-List Status, Name, DisplayName

$listen = netstat -ano | Select-String ":5432\s+.*LISTENING"
if ($listen) {
    Write-Host "Puerto 5432 en escucha:" -ForegroundColor Green
    $listen | ForEach-Object { $_.Line }
} else {
    Write-Host "AVISO: no se detecto LISTENING en 5432. Revisa el Visor de eventos -> Registro de aplicacion (PostgreSQL)." -ForegroundColor Yellow
}

Write-Host "`nListo. En pgAdmin: Add New Server -> Host localhost, Port 5432, Maintenance DB postgres, usuario postgres." -ForegroundColor Green
