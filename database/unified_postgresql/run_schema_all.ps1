#Requires -Version 5.1
<#
.SYNOPSIS
  Aplica todos los scripts SQL del esquema unificado sobre la base equipo05_unificado.

.DESCRIPTION
  Requiere psql en PATH (PostgreSQL client). Define PGPASSWORD en el entorno o pasalo con -Password.
  La base debe existir (ejecutar antes 00_create_database.sql contra la base "postgres").

.EXAMPLE
  $env:PGPASSWORD = "tu_clave"
  .\run_schema_all.ps1 -PgUser postgres -PgDatabase equipo05_unificado
#>
param(
    [string] $PgHost = "127.0.0.1",
    [int] $PgPort = 5432,
    [string] $PgUser = "postgres",
    [string] $PgDatabase = "equipo05_unificado",
    [string] $Password = ""
)

$ErrorActionPreference = "Stop"
$here = $PSScriptRoot

if ($Password) {
    $env:PGPASSWORD = $Password
}
if (-not $env:PGPASSWORD) {
    Write-Host "AVISO: PGPASSWORD no esta definido. psql puede pedir contrasena interactiva." -ForegroundColor Yellow
}

$ordered = @(
    "01_extensions_and_schemas.sql",
    "00_core_auth.sql",
    "04_mod_inventario_transparencia.sql",
    "02_mod_incendios.sql",
    "03_mod_rescate.sql",
    "06_mod_logistica.sql",
    "07_mod_seguimiento.sql",
    "08_mod_cuadrillas.sql"
)

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
