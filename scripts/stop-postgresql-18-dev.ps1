# Detiene PostgreSQL 18 iniciado con pg_ctl (no toca el servicio de Windows).
# Uso: .\scripts\stop-postgresql-18-dev.ps1

$ErrorActionPreference = "Stop"
$dataDir = "C:\Program Files\PostgreSQL\18\data"
$pgCtl  = "C:\Program Files\PostgreSQL\18\bin\pg_ctl.exe"

if (-not (Test-Path $pgCtl)) {
    Write-Error "No se encontro $pgCtl."
    exit 1
}

Write-Host "Deteniendo PostgreSQL (pg_ctl stop)..." -ForegroundColor Cyan
& $pgCtl stop -D $dataDir -m fast
Write-Host "Hecho." -ForegroundColor Green
