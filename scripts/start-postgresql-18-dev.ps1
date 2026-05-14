# Inicia PostgreSQL 18 sin usar el servicio de Windows (pg_ctl).
# Ejecutar en PowerShell normal (no hace falta admin si tenes permisos sobre la carpeta data).
# Uso: .\scripts\start-postgresql-18-dev.ps1

$ErrorActionPreference = "Stop"
$dataDir = "C:\Program Files\PostgreSQL\18\data"
$pgCtl  = "C:\Program Files\PostgreSQL\18\bin\pg_ctl.exe"
$log    = Join-Path $dataDir "pg_ctl_dev.log"

if (-not (Test-Path $pgCtl)) {
    Write-Error "No se encontro $pgCtl. Ajusta la version (18) en el script."
    exit 1
}

$status = & $pgCtl status -D $dataDir 2>&1
if ($LASTEXITCODE -eq 0 -and "$status" -match "servidor") {
    Write-Host "PostgreSQL ya esta en ejecucion (pg_ctl status OK)." -ForegroundColor Green
    exit 0
}

Write-Host "Iniciando PostgreSQL (pg_ctl)..." -ForegroundColor Cyan
& $pgCtl start -D $dataDir -l $log -w -t 30
if ($LASTEXITCODE -ne 0) {
    Write-Error "pg_ctl start fallo. Revisa: $log"
    exit $LASTEXITCODE
}

Write-Host "Listo. Escucha en 127.0.0.1:5432 (conecta con pgAdmin o psql)." -ForegroundColor Green
Write-Host "Para detener: .\scripts\stop-postgresql-18-dev.ps1" -ForegroundColor DarkGray
