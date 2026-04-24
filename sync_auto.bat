@echo off
title Sincronización automática Laravel

echo ============================================
echo   INICIANDO SINCRONIZACION AUTOMATICA
echo   Laravel Scheduler - ejecutando cada 60s
echo ============================================
echo.

:loop
echo [ %date% %time% ] Ejecutando scheduler...
php artisan schedule:run

echo.
echo Esperando 60 segundos...
timeout /t 60 > NUL

goto loop
