#Requires -Version 5.1
<#
.SYNOPSIS
  Onboarding completo de base de datos (PostgreSQL unificado).

.EXAMPLE
  .\scripts\onboard-db.ps1
  .\scripts\onboard-db.ps1 -Seed -WaitDb 90
#>
param(
    [switch] $FreshInventario,
    [switch] $Seed,
    [switch] $SkipSeed,
    [int] $WaitDb = 60
)

$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "Creado .env desde .env.example" -ForegroundColor Yellow
}

Write-Host ">> Levantando db_unificado (Docker)..." -ForegroundColor Cyan
docker compose up -d db_unificado

$args = @("artisan", "db:onboard", "--wait-db=$WaitDb")
if ($FreshInventario) { $args += "--fresh-inventario" }
if ($Seed) { $args += "--seed" }
if ($SkipSeed) { $args += "--skip-seed" }

Write-Host ">> php artisan db:onboard ..." -ForegroundColor Cyan
php @args

Write-Host ">> Verificacion..." -ForegroundColor Cyan
php scripts/verify-unified-modules.php

Write-Host "`nListo. Inicia con: php artisan serve" -ForegroundColor Green
