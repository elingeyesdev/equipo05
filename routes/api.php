<?php

use Illuminate\Support\Facades\Route;

Route::prefix('inventario')
    ->group(function () {
        require base_path('modulos/donacion-recepcion-inventario-main/routes/api.php');
    });

Route::prefix('incendios')
    ->group(function () {
        require base_path('modulos/monitoreo-incendios-simulacion-main/routes/api.php');
    });

Route::prefix('rescate')
    ->group(function () {
        require base_path('modulos/rescate-animales-silvestres-main/routes/api.php');
    });

Route::prefix('logistica')
    ->group(function () {
        require base_path('modulos/logistica-transportacion-donaciones-main/routes/api.php');
    });
