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
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/status', fn () => response()->json([
            'modulo' => 'rescate-animales-silvestres-main',
            'estado' => 'integrado_en_repositorio',
            'siguiente_paso' => 'adaptacion_de_controladores_a_login_unico',
        ]));
    });
