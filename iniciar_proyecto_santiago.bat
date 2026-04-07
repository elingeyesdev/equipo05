@echo off
setlocal
cd /d "%~dp0"

echo ===============================================
echo   Sistema de Gestion de Incendios - Inicio
echo ===============================================
echo.

where php >nul 2>&1
if errorlevel 1 (
    echo [ERROR] PHP no esta disponible en PATH.
    echo Instala PHP o abre este proyecto con XAMPP habilitado.
    pause
    exit /b 1
)

if not exist "vendor\autoload.php" (
    echo [INFO] Instalando dependencias de Composer...
    if exist "C:\ProgramData\ComposerSetup\bin\composer.phar" (
        php "C:\ProgramData\ComposerSetup\bin\composer.phar" install
    ) else (
        composer install
    )

    if errorlevel 1 (
        echo [ERROR] No se pudo completar composer install.
        pause
        exit /b 1
    )
)

if not exist ".env" (
    echo [INFO] Creando archivo .env...
    copy ".env.example" ".env" >nul
)

if not exist "database\database.sqlite" (
    echo [INFO] Creando base de datos SQLite local...
    type nul > "database\database.sqlite"
)

echo [INFO] Generando APP_KEY...
php artisan key:generate --force
if errorlevel 1 (
    echo [ERROR] No se pudo generar APP_KEY.
    pause
    exit /b 1
)

echo [INFO] Ejecutando migraciones...
php artisan migrate --force
if errorlevel 1 (
    echo [ERROR] Fallaron las migraciones.
    pause
    exit /b 1
)

echo [INFO] Cargando datos de prueba...
php artisan db:seed --force
if errorlevel 1 (
    echo [ERROR] Fallo la carga de datos de prueba.
    pause
    exit /b 1
)

echo.
echo [OK] Proyecto listo.
echo [INFO] Abriendo modulo de monitoreo en el navegador...
start "" "http://127.0.0.1:8000/monitoreo"
echo [INFO] Iniciando servidor Laravel en http://127.0.0.1:8000
echo [INFO] Presiona CTRL + C para detenerlo.
echo.

php artisan serve --host=127.0.0.1 --port=8000

endlocal
