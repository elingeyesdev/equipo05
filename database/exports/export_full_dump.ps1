#Requires -Version 5.1
<#
.SYNOPSIS
  Exporta la base PostgreSQL unificada a un archivo .sql (estructura + datos).

.EXAMPLE
  .\export_full_dump.ps1
  .\export_full_dump.ps1 -OutputFile "..\equipo05_unificado_20260529.sql"
#>
param(
    [string] $ContainerName = "equipo05-unificado-pg",
    [string] $Database = "equipo05_unificado",
    [string] $PgUser = "postgres",
    [string] $OutputFile = ""
)

$ErrorActionPreference = "Stop"
$here = $PSScriptRoot

if (-not $OutputFile) {
    $stamp = Get-Date -Format "yyyyMMddHHmm"
    $OutputFile = Join-Path $here "equipo05_unificado_full_dump_$stamp.sql"
}

$running = docker ps --filter "name=$ContainerName" --format "{{.Names}}" 2>$null
if (-not $running) {
    Write-Error "El contenedor '$ContainerName' no esta en ejecucion. Levanta Docker: docker compose up -d db_unificado"
    exit 1
}

Write-Host "Exportando $Database desde $ContainerName ..." -ForegroundColor Cyan
docker exec $ContainerName pg_dump -U $PgUser -d $Database --no-owner --no-acl -F p 2>&1 |
    Out-File -FilePath $OutputFile -Encoding utf8

$sizeKb = [math]::Round((Get-Item $OutputFile).Length / 1KB, 1)
Write-Host "Listo: $OutputFile ($sizeKb KB)" -ForegroundColor Green
Write-Host "Envia ese archivo junto con LEEME_RESTAURAR.txt y el repo del proyecto."
