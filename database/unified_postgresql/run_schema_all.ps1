#Requires -Version 5.1
<#
.SYNOPSIS
  Aplica todos los scripts SQL del esquema unificado sobre la base equipo05_unificado.

.DESCRIPTION
  Orden canonico: database/unified_postgresql/schema_order.json (mismo que Docker init).
  Requiere psql en PATH. La base debe existir (00_create_database.sql contra postgres).

.EXAMPLE
  $env:PGPASSWORD = "equipo05_unificado_dev"
  .\run_schema_all.ps1 -PgPort 5433
#>
param(
    [string] $PgHost = "127.0.0.1",
    [int] $PgPort = 5433,
    [string] $PgUser = "postgres",
    [string] $PgDatabase = "equipo05_unificado",
    [string] $Password = ""
)

$ErrorActionPreference = "Stop"
$here = $PSScriptRoot
$manifest = Join-Path $here "schema_order.json"

if ($Password) {
    $env:PGPASSWORD = $Password
}
if (-not $env:PGPASSWORD) {
    Write-Host "AVISO: PGPASSWORD no esta definido. psql puede pedir contrasena." -ForegroundColor Yellow
}

if (-not (Test-Path $manifest)) {
    Write-Error "No se encontro schema_order.json en $here"
    exit 1
}

$ordered = Get-Content $manifest -Raw | ConvertFrom-Json

foreach ($f in $ordered) {
    $path = Join-Path $here $f
    if (-not (Test-Path $path)) {
        Write-Error "No se encontro el archivo: $path"
        exit 1
    }
    Write-Host ">> Ejecutando $f ..." -ForegroundColor Cyan
    & psql -h $PgHost -p $PgPort -U $PgUser -d $PgDatabase -v ON_ERROR_STOP=1 -f $path
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Fallo al ejecutar $f (codigo $LASTEXITCODE)"
        exit $LASTEXITCODE
    }
}

Write-Host "Listo: esquemas aplicados en $PgDatabase" -ForegroundColor Green
Write-Host "Siguiente paso: php artisan db:onboard --seed" -ForegroundColor Green
