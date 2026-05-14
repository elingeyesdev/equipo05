# Diagnostico PostgreSQL 18 (Windows) - no requiere administrador
# Ejecutar en PowerShell: .\scripts\diagnostico-postgresql-18.ps1

$ErrorActionPreference = "Continue"
$svcName = "postgresql-x64-18"
$dataDir = "C:\Program Files\PostgreSQL\18\data"

Write-Host "`n=== 1. Servicio Windows ===" -ForegroundColor Cyan
Get-Service -Name $svcName -ErrorAction SilentlyContinue | Format-List Name, Status, StartType

Write-Host "=== 2. Cuenta con la que arranca el servicio (sc qc) ===" -ForegroundColor Cyan
& sc.exe qc $svcName 2>&1 | Select-String -Pattern "NOMBRE_INICIO|BINARY"

Write-Host "=== 3. Puerto 5432 ===" -ForegroundColor Cyan
$ns = netstat -ano | Select-String ":5432"
if ($ns) { $ns } else { "(nadie escuchando en 5432)" }

Write-Host "=== 4. postmaster.pid (si existe con servicio detenido, revisar) ===" -ForegroundColor Cyan
$pidFile = Join-Path $dataDir "postmaster.pid"
if (Test-Path $pidFile) { Get-Content $pidFile } else { "(no existe; normal si el servidor esta apagado)" }

Write-Host "=== 5. Ultimas lineas del log de PostgreSQL ===" -ForegroundColor Cyan
$logDir = Join-Path $dataDir "log"
$last = Get-ChildItem $logDir -Filter "*.log" -ErrorAction SilentlyContinue | Sort-Object LastWriteTime -Descending | Select-Object -First 1
if ($last) { Get-Content $last.FullName -Tail 15 } else { "(sin carpeta log o vacia)" }

Write-Host "`n=== Que hacer ===" -ForegroundColor Green
Write-Host "A) Servicios (GUI): Win+R -> services.msc -> $svcName -> Propiedades -> Iniciar sesion ->"
Write-Host "   'Cuenta del sistema local' -> Aplicar -> pestaña General -> Iniciar."
Write-Host "B) Script reparacion (PowerShell COMO ADMINISTRADOR):"
Write-Host "   .\scripts\fix-postgresql-18-windows-service.ps1"
Write-Host "C) Si sigue fallando: Visor de eventos, Registros de Windows, Aplicacion, buscar PostgreSQL."
Write-Host ""
