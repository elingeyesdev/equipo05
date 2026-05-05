<?php

use Illuminate\Support\Facades\Route;
use Modules\Incendios\Http\Controllers\Api\AuthController;
use Modules\Incendios\Http\Controllers\Api\BiomasaController;
use Modules\Incendios\Http\Controllers\Api\FocosIncendioController;
use Modules\Incendios\Http\Controllers\Api\TipoBiomasaController;
use Modules\Incendios\Http\Controllers\Api\SimulacionController;
use Modules\Incendios\Http\Controllers\Api\PredictionController;
use Modules\Incendios\Http\Controllers\Api\WeatherController;
use Modules\Incendios\Http\Controllers\Api\FiresController;
use Modules\Incendios\Http\Controllers\Api\PublicFirmsController;
use Modules\Incendios\Http\Controllers\Api\PublicPredictionsController;
use Modules\Incendios\Http\Controllers\Api\UserActivityController;

/*
|--------------------------------------------------------------------------
| API Routes - SIPII Consolidated
|--------------------------------------------------------------------------
|
| Unified REST API for both web panel and Flutter mobile app.
| Includes authentication (Sanctum), CRUD operations, and external data.
|
*/

// ============================================================================
// AUTHENTICATION ENDPOINTS (Sanctum)
// ============================================================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected endpoints - require authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// ============================================================================
// EXTERNAL DATA ENDPOINTS (Weather & Fire Data)
// ============================================================================
// Weather API - Open-Meteo (current & historical)
Route::get('/weather', [WeatherController::class, 'index'])->name('api.weather');

// Fire data API - NASA FIRMS
Route::get('/fires', [FiresController::class, 'index'])->name('api.fires');

// ============================================================================
// PUBLIC ENDPOINTS (no authentication required)
// ============================================================================
Route::prefix('public')->group(function () {
    // Real-time FIRMS fire data (updated every 5 minutes by scheduler)
    Route::get('/firms/active', [PublicFirmsController::class, 'getActiveFires'])->name('api.public.firms.active');
    Route::get('/firms/statistics', [PublicFirmsController::class, 'getStatistics'])->name('api.public.firms.stats');
    Route::get('/firms/latest', [PublicFirmsController::class, 'getLatestFromFirms'])->name('api.public.firms.latest');
    Route::get('/firms/geojson', [PublicFirmsController::class, 'getGeoJson'])->name('api.public.firms.geojson');
    Route::get('/firms/health', [PublicFirmsController::class, 'healthCheck'])->name('api.public.firms.health');
    
    // Public Predictions API (for external projects)
    Route::get('/predictions', [PublicPredictionsController::class, 'index'])->name('api.public.predictions.index');
    Route::get('/predictions/latest', [PublicPredictionsController::class, 'latest'])->name('api.public.predictions.latest');
    Route::get('/predictions/statistics', [PublicPredictionsController::class, 'statistics'])->name('api.public.predictions.stats');
    Route::get('/predictions/geojson', [PublicPredictionsController::class, 'geojson'])->name('api.public.predictions.geojson');
    Route::get('/predictions/lookup', [PublicPredictionsController::class, 'lookup'])->name('api.public.predictions.lookup');
    Route::get('/predictions/{id}', [PublicPredictionsController::class, 'show'])->name('api.public.predictions.show');
    Route::get('/predictions/foco/{focoId}', [PublicPredictionsController::class, 'byFoco'])->name('api.public.predictions.by-foco');
    
    // Legacy endpoints
    Route::get('/focos-incendios', [FocosIncendioController::class, 'index']);
    Route::get('/biomasas', [BiomasaController::class, 'index']);
    Route::get('/tipos-biomasa', [TipoBiomasaController::class, 'index']);
    
    // Weather endpoint for external systems
    Route::get('/weather', [WeatherController::class, 'index'])->name('api.public.weather');
    
    // PDF Generation endpoints (accessible to all users)
    Route::get('/predictions/{id}/pdf', [PredictionController::class, 'generatePdf'])->name('api.predictions.pdf');
    Route::get('/simulaciones/{id}/pdf', [SimulacionController::class, 'generatePdf'])->name('api.simulaciones.pdf');

    
});

// ============================================================================
// PROTECTED API RESOURCES (require authentication)
// ============================================================================
// Public trazabilidad por CI (sin middleware ni prefijo /public)
Route::get('/trazabilidad/voluntario/{ci}', [UserActivityController::class, 'getActivitiesByCi'])
    ->name('api.trazabilidad.voluntario');

// Buscar por ubicación (pública)
Route::get('/trazabilidad/ubicacion/{value}', [UserActivityController::class, 'getActivitiesByUbicacion'])
    ->name('api.trazabilidad.ubicacion');

Route::middleware('auth:sanctum')->group(function () {
    // Biomasas - Full CRUD
    Route::apiResource('biomasas', BiomasaController::class)->names([
        'index' => 'api.biomasas.index',
        'store' => 'api.biomasas.store',
        'show' => 'api.biomasas.show',
        'update' => 'api.biomasas.update',
        'destroy' => 'api.biomasas.destroy',
    ]);
    
    // Focos de Incendio - Full CRUD
    Route::apiResource('focos-incendios', FocosIncendioController::class)->names([
        'index' => 'api.focos-incendios.index',
        'store' => 'api.focos-incendios.store',
        'show' => 'api.focos-incendios.show',
        'update' => 'api.focos-incendios.update',
        'destroy' => 'api.focos-incendios.destroy',
    ]);
    
    // Predictions - Full CRUD
    Route::apiResource('predictions', PredictionController::class)->names([
        'index' => 'api.predictions.index',
        'store' => 'api.predictions.store',
        'show' => 'api.predictions.show',
        'update' => 'api.predictions.update',
        'destroy' => 'api.predictions.destroy',
    ]);
    
    Route::apiResource('tipos-biomasa', TipoBiomasaController::class)->names([
        'index' => 'api.tipos-biomasa.index',
        'store' => 'api.tipos-biomasa.store',
        'show' => 'api.tipos-biomasa.show',
        'update' => 'api.tipos-biomasa.update',
        'destroy' => 'api.tipos-biomasa.destroy',
    ]);

    Route::apiResource('simulaciones', SimulacionController::class)->names([
        'index' => 'api.simulaciones.index',
        'store' => 'api.simulaciones.store',
        'show' => 'api.simulaciones.show',
        'update' => 'api.simulaciones.update',
        'destroy' => 'api.simulaciones.destroy',
    ]);
});

// ============================================================================
// USER ACTIVITY TRACKING BY CI (Cédula de Identidad)
// ============================================================================
Route::middleware('auth:sanctum')->group(function () {
    // Mis propias actividades (cualquier usuario autenticado)
    Route::get('/trazabilidad/mis-actividades', [UserActivityController::class, 'getMyActivities'])
        ->name('api.trazabilidad.mis-actividades');
    
    Route::get('/trazabilidad/mis-estadisticas', [UserActivityController::class, 'getMyStats'])
        ->name('api.trazabilidad.mis-estadisticas');
    
    Route::get('/trazabilidad/estadisticas/{ci}', [UserActivityController::class, 'getStatsByCi'])
        ->name('api.trazabilidad.estadisticas');
});
