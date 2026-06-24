<?php

use App\Http\Controllers\Api\Auth\CitizenAuthController;
use App\Http\Controllers\Api\Public\LogisticaPublicoController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', [CitizenAuthController::class, 'login']);

Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::get('me', [CitizenAuthController::class, 'me']);
    Route::post('logout', [CitizenAuthController::class, 'logout']);
    Route::get('mis-solicitudes', [CitizenAuthController::class, 'misSolicitudes']);
    Route::get('mis-hallazgos', [CitizenAuthController::class, 'misHallazgos']);
});

Route::prefix('public/logistica')->group(function () {
    Route::post('solicitudes', [LogisticaPublicoController::class, 'storeSolicitud']);
    Route::get('solicitudes/{codigo}', [LogisticaPublicoController::class, 'showSolicitud'])
        ->where('codigo', 'SOL-[0-9]+');
    Route::get('galeria', [LogisticaPublicoController::class, 'galeria']);
    Route::get('paquete/{codigo}/ruta', [LogisticaPublicoController::class, 'rutaPaquete'])
        ->where('codigo', '.+');
});

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

Route::prefix('seguimiento')
    ->group(function () {
        require base_path('modulos/seguimiento-voluntarios-comunarios-main/routes/api.php');
    });

Route::prefix('cuadrillas')
    ->group(function () {
        require base_path('modulos/cuadrillas-incendios-kardex-cursos-Alas-Chiquitanas/routes/api.php');
    });
