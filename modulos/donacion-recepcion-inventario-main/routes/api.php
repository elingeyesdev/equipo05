<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Inventario\Http\Controllers\Api\Auth\DonanteAuthController;
use Modules\Inventario\Http\Controllers\Api\Auth\VoluntarioAuthController;
use Modules\Inventario\Http\Controllers\Api\DonacionController;
use Modules\Inventario\Http\Controllers\Api\CampanaController;
use Modules\Inventario\Http\Controllers\Api\PuntoRecoleccionController;
use Modules\Inventario\Http\Controllers\Api\AlmacenController;
use Modules\Inventario\Http\Controllers\Api\EstanteController;
use Modules\Inventario\Http\Controllers\Api\InventarioController;
use Modules\Inventario\Http\Controllers\Api\DashboardController;
use Modules\Inventario\Http\Controllers\Api\SolicitudRecoleccionController;
use Modules\Inventario\Http\Controllers\Api\ImagenController;
use Modules\Inventario\Http\Controllers\Api\UserController;
use Modules\Inventario\Http\Controllers\Api\CategoriaController;
use Modules\Inventario\Http\Controllers\Api\DonanteController;
use Modules\Inventario\Http\Controllers\Api\TrazabilidadController;
use Modules\Inventario\Http\Controllers\Api\PaqueteController;
use Modules\Inventario\Http\Controllers\Auth\RegistroSimpleController;

// Rutas públicas de autenticación
Route::post('/donante-auth/login', [DonanteAuthController::class, 'login']);
Route::post('/auth/login', [VoluntarioAuthController::class, 'login']);

// Ruta pública para gateway (búsqueda de voluntarios por CI)
Route::get('registro/ci/{ci}', [RegistroSimpleController::class, 'showByCi']);

// Rutas públicas
Route::get('/inventario/por-producto', [InventarioController::class, 'getInventoryByProduct']);
Route::get('/campanas', [CampanaController::class, 'index']);
Route::get('/campanas/{id}', [CampanaController::class, 'show']);
Route::get('/donaciones/dinero', [DonacionController::class, 'getAllMoneyDonations']);
Route::get('/donaciones/especie', [DonacionController::class, 'getAllInKindDonations']);
Route::get('/donaciones/especie/{id}/detalle', [DonacionController::class, 'getInKindDonationDetail']);

// Nuevos endpoints públicos
Route::get('/categorias', [CategoriaController::class, 'getAllWithProducts']);
Route::get('/almacenes-completo', [AlmacenController::class, 'getAllWithStructure']);
Route::get('/donantes', [DonanteController::class, 'index']);

// Rutas protegidas con autenticación Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('/donante-auth/logout', [DonanteAuthController::class, 'logout']);
    Route::post('/donante-auth/change-password', [DonanteAuthController::class, 'changePassword']);
    Route::post('/auth/logout', [VoluntarioAuthController::class, 'logout']);

    // Usuarios
    Route::get('/users/{userId}', [UserController::class, 'show']);
    Route::apiResource('users', UserController::class);

    // Donaciones
    Route::post('/donaciones', [DonacionController::class, 'store']);
    Route::post('/donaciones-en-dinero', [DonacionController::class, 'createMoneyDonation']);
    Route::put('/donaciones-en-dinero/{id}', [DonacionController::class, 'updateMoneyDonation']);
    Route::get('/donantes/{id}/donaciones', [DonacionController::class, 'getByDonante']);
    Route::get('/donaciones/donante/{id}', [DonacionController::class, 'getByDonante']); // Alias para compatibilidad
    Route::get('/donaciones-en-dinero/getAllById/{id}', [DonacionController::class, 'getMoneyDonationsByDonante']);
    Route::get('/donaciones/dinero/donante/{id}', [DonacionController::class, 'getMoneyDonationsByDonante']); // Alias para compatibilidad
    Route::patch('/donaciones/estado/{id}', [DonacionController::class, 'updateEstado']);
    Route::apiResource('donaciones', DonacionController::class)->names('api.donaciones');

    // Campañas (solo operaciones que requieren autenticación)
    Route::post('/campanas', [CampanaController::class, 'store']);
    Route::put('/campanas/{id}', [CampanaController::class, 'update']);
    Route::delete('/campanas/{id}', [CampanaController::class, 'destroy']);

    // Puntos de recolección
    Route::get('/puntos-de-recoleccion/campana/{id}', [PuntoRecoleccionController::class, 'getByCampana']);
    Route::apiResource('puntos-de-recoleccion', PuntoRecoleccionController::class);

    // Almacenes
    Route::get('/almacenes', [AlmacenController::class, 'index']);
    Route::apiResource('almacenes', AlmacenController::class)->except(['index']);

    // Estantes
    Route::get('/estantes', [EstanteController::class, 'index']);
    Route::get('/estantes/almacen/{id}', [EstanteController::class, 'getByAlmacen']);
    Route::apiResource('estantes', EstanteController::class)->except(['index']);

    // Inventario
    Route::get('/inventario/stock', [InventarioController::class, 'getStock']);
    Route::get('/inventario/stock/articulo/{id}', [InventarioController::class, 'getStockByArticulo']);
    Route::get('/inventario/stock/estante/{id}', [InventarioController::class, 'getStockByEstante']);
    Route::apiResource('inventario', InventarioController::class);

    // Dashboard
    Route::get('/dashboard/total-donaciones', [DashboardController::class, 'getTotalDonaciones']);
    Route::get('/dashboard/donaciones-por-mes/{year}', [DashboardController::class, 'getDonacionesPorMes']);

    // Solicitudes de recolección
    Route::post('/solicitudesRecoleccion', [SolicitudRecoleccionController::class, 'store']);
    Route::get('/solicitudesRecoleccion/donante/{id}', [SolicitudRecoleccionController::class, 'getByDonante']);
    Route::apiResource('solicitudesRecoleccion', SolicitudRecoleccionController::class)->except(['store']);

    // Imágenes
    Route::post('/imagenes-solicitud-recogida', [ImagenController::class, 'upload']);
    Route::post('/upload-comprobante', [DonacionController::class, 'uploadComprobante']);
    Route::apiResource('imagenes-solicitud-recogida', ImagenController::class);
});

// Trazabilidad - acciones realizadas por un voluntario (público)
Route::get('/trazabilidad/voluntario/{ci}', [TrazabilidadController::class, 'porVoluntario']);

// Paquetes - información completa por código (público)
Route::get('/trazabilidad/paquete/{codigo}', [PaqueteController::class, 'porCodigo']);





