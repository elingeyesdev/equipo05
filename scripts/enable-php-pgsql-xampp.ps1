# Habilita pdo_pgsql en XAMPP (Windows) para Laravel + PostgreSQL unificado.
# Ejecutar PowerShell como administrador si php.ini no es editable.
# Uso: .\scripts\enable-php-pgsql-xampp.ps1

$ErrorActionPreference = "Stop"
$phpIni = (php --ini | Select-String "Loaded Configuration File").ToString() -replace "^\s*Loaded Configuration File:\s*", ""

if (-not $phpIni -or -not (Test-Path $phpIni)) {
    Write-Error "No se encontro php.ini. Ejecuta desde una terminal donde 'php' este en PATH."
    exit 1
}

Write-Host "php.ini: $phpIni" -ForegroundColor Cyan
$content = Get-Content $phpIni -Raw
$changed = $false

foreach ($pair in @(
    @{ Old = ';extension=pdo_pgsql'; New = 'extension=pdo_pgsql' },
    @{ Old = ';extension=pgsql'; New = 'extension=pgsql' }
)) {
    if ($content -match [regex]::Escape($pair.Old)) {
        $content = $content.Replace($pair.Old, $pair.New)
        $changed = $true
        Write-Host "Activado: $($pair.New)" -ForegroundColor Green
    } elseif ($content -match [regex]::Escape($pair.New)) {
        Write-Host "Ya activo: $($pair.New)" -ForegroundColor DarkGray
    }
}

if (-not $changed) {
    Write-Host "No se encontraron lineas comentadas de pgsql; revisa php.ini manualmente." -ForegroundColor Yellow
} else {
    Set-Content -Path $phpIni -Value $content -NoNewline
}

Write-Host "`nExtensiones PDO cargadas:" -ForegroundColor Cyan
php -m | Select-String -Pattern "pdo_pgsql|pgsql"

if (-not (php -m | Select-String -Pattern "^pdo_pgsql$")) {
    Write-Error "pdo_pgsql sigue sin cargar. Reinicia la terminal y vuelve a probar; si usas Apache, reinicia XAMPP."
    exit 1
}

Write-Host "`nListo. Ejecuta: php artisan config:clear" -ForegroundColor Green
