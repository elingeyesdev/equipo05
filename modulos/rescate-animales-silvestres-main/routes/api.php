<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Rescate\Http\Controllers\Api\ReportApiController;
use Modules\Rescate\Http\Controllers\Api\AnimalFileApiController;
use Modules\Rescate\Http\Controllers\Api\UserApiController;
use Modules\Rescate\Http\Controllers\Api\AuthApiController;
use Modules\Rescate\Http\Controllers\Api\AnimalCareApiController;
use Modules\Rescate\Http\Controllers\Api\AnimalFeedingApiController;
use Modules\Rescate\Http\Controllers\Api\AnimalMedicalEvaluationApiController;
use Modules\Rescate\Http\Controllers\Api\AnimalHistoryApiController;
use Modules\Rescate\Http\Controllers\Api\TransferApiController;
use Modules\Rescate\Http\Controllers\Api\ReleaseApiController;
use Modules\Rescate\Http\Controllers\Api\CenterApiController;
use Modules\Rescate\Http\Controllers\Api\SpeciesApiController;
use Modules\Rescate\Http\Controllers\Api\AnimalStatusApiController;
use Modules\Rescate\Http\Controllers\Api\VeterinarianApiController;
use Modules\Rescate\Http\Controllers\Api\TreatmentTypeApiController;
use Modules\Rescate\Http\Controllers\Api\CareTypeApiController;
use Modules\Rescate\Http\Controllers\Api\FirePredictionApiController;
use Modules\Rescate\Http\Controllers\Api\AnimalConditionApiController;
use Modules\Rescate\Http\Controllers\Api\IncidentTypeApiController;
use Modules\Rescate\Http\Controllers\Api\RescuerApiController;
use Modules\Rescate\Http\Controllers\Api\AnimalApiController;
use Modules\Rescate\Http\Controllers\Api\PersonApiController;
use Modules\Rescate\Http\Controllers\Api\WeatherApiController;
use Modules\Rescate\Http\Controllers\TrazabilidadController;
use Modules\Rescate\Http\Controllers\Auth\RegistroSimpleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Ruta pública para reports (sin autenticación)
Route::post('/reports', [ReportApiController::class, 'store'])->name('api.reports.public');
Route::get('/species', [SpeciesApiController::class, 'index'])->name('api.species.public');
Route::get('/users/ci', [UserApiController::class, 'getAllCis'])->name('api.users.cis.public');

Route::name('api.')->group(function () {
    Route::apiResource('login', AuthApiController::class)->only(['store']);
    Route::apiResource('reports', ReportApiController::class)->except(['store']);
    Route::apiResource('animals', AnimalApiController::class)->only(['index', 'show']);
    Route::apiResource('animal-files', AnimalFileApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('animal-cares', AnimalCareApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('animal-feedings', AnimalFeedingApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('animal-medical-evaluations', AnimalMedicalEvaluationApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('animal-histories', AnimalHistoryApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('transfers', TransferApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('releases', ReleaseApiController::class)->only(['index', 'show', 'store']);
    Route::apiResource('users', UserApiController::class);
    Route::apiResource('people', PersonApiController::class)->only(['index', 'show']);
    Route::apiResource('centers', CenterApiController::class)->only(['index', 'show']);
    Route::apiResource('species', SpeciesApiController::class)->only(['show']);
    Route::apiResource('animal-statuses', AnimalStatusApiController::class);
    Route::apiResource('veterinarians', VeterinarianApiController::class)->only(['index', 'show']);
    Route::apiResource('rescuers', RescuerApiController::class)->only(['index', 'show']);
    Route::apiResource('treatment-types', TreatmentTypeApiController::class);
    Route::apiResource('care-types', CareTypeApiController::class);
    Route::apiResource('fire-predictions', FirePredictionApiController::class)->only(['index']);
    Route::apiResource('animal-conditions', AnimalConditionApiController::class);
    Route::apiResource('incident-types', IncidentTypeApiController::class);
    
    Route::get('weather', [WeatherApiController::class, 'index'])->name('weather');
});

// Rutas de trazabilidad (fuera del grupo api. para mantener la estructura del PDF)
Route::get('/trazabilidad/voluntario/{ci}', [TrazabilidadController::class, 'porVoluntario']);
Route::get('/trazabilidad/provincia/{provincia}', [TrazabilidadController::class, 'porProvincia']);
Route::get('/trazabilidad/animales/especie/{especie}', [TrazabilidadController::class, 'porEspecie']);
Route::get('/trazabilidad/animales/liberados', [TrazabilidadController::class, 'porLiberados']);

// Ruta para búsqueda por CI para el API Gateway
Route::get('registro/ci/{ci}', [RegistroSimpleController::class, 'showByCi']);
