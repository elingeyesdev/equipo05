#Requires -Version 5.1
<#
.SYNOPSIS
  Restaura un dump .sql en la base equipo05_unificado (Docker).

.EXAMPLE
  .\restore_full_dump.ps1 -SqlFile ".\equipo05_unificado_full_dump.sql"
#>
param(
    [Parameter(Mandatory = $true)]
    [string] $SqlFile,
    [string] $ContainerName = "equipo05-unificado-pg",
    [string] $Database = "equipo05_unificado",
    [string] $PgUser = "postgres"
)

$ErrorActionPreference = "Stop"

if (-not (Test-Path $SqlFile)) {
    Write-Error "No existe el archivo: $SqlFile"
    exit 1
}

$running = docker ps --filter "name=$ContainerName" --format "{{.Names}}" 2>$null
if (-not $running) {
    Write-Error "Levanta primero: docker compose up -d db_unificado"
    exit 1
}

$remote = "/tmp/restore_dump.sql"
Write-Host "Copiando dump al contenedor ..." -ForegroundColor Cyan
docker cp $SqlFile "${ContainerName}:$remote"

Write-Host "Restaurando en $Database (puede tardar unos minutos) ..." -ForegroundColor Cyan
docker exec $ContainerName psql -U $PgUser -d $Database -v ON_ERROR_STOP=1 -f $remote
if ($LASTEXITCODE -ne 0) {
    Write-Error "Fallo la restauracion (codigo $LASTEXITCODE)"
    exit $LASTEXITCODE
}

docker exec $ContainerName rm -f $remote
Write-Host "Restauracion completada." -ForegroundColor Green
Write-Host "Configura .env con DATABASE_UNIFIED_POSTGRES=true y UNIFIED_PG_PORT=5433"
