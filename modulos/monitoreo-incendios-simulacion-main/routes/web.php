<?php

use App\Support\FusionModuloAccess;
use Illuminate\Support\Facades\Route;

// Authentication is centralized in the main application.

// Google OAuth routes
Route::get('/auth/google', [\Modules\Incendios\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])
    ->name('google.redirect')
    ->middleware('guest');

Route::get('/auth/google/callback', [\Modules\Incendios\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])
    ->name('google.callback')
    ->middleware('guest');

// Public/Guest routes
Route::middleware('guest')->group(function () {
    // Redirect root to login if not authenticated
});

// Ruta pública para compartir simulaciones (no requiere autenticación)
Route::get('simulaciones/public/{id}', [Modules\Incendios\Http\Controllers\SimulacioneController::class, 'publicSimulation'])
    ->name('simulaciones.public');

// Authenticated routes
Route::middleware(['auth', 'permission.check:'.FusionModuloAccess::INCENDIOS_PERMISSIONS])->group(function () {

    // Dashboard - accessible to all authenticated users
    Route::get('/', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    // Datos Climáticos - históricos de la última semana
    Route::get('/datos-climaticos', [\Modules\Incendios\Http\Controllers\DatosClimaticosController::class, 'index'])
        ->name('datos-climaticos.index');

    // Biomasas GeoJSON endpoint for map
    Route::get('/dashboard/biomasas', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'getBiomasas'])
        ->name('dashboard.biomasas');

    Route::get('/dashboard/focos', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'getFocosForMap'])
        ->name('dashboard.focos');

    // Clear dashboard cache
    Route::post('/dashboard/clear-cache', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'clearCache'])
        ->name('dashboard.clear-cache');

    // Reports - accessible to all authenticated users
    Route::get('/reports/fires', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'firesActivityReport'])
        ->name('reports.fires');
    Route::get('/reports/fires/export-excel', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'firesActivityExportExcel'])
        ->name('reports.fires.export-excel');
    Route::get('/reports/fires/export-pdf', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'firesActivityExportPdf'])
        ->name('reports.fires.export-pdf');

    Route::get('/reports/biomasas', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'biomasasManagementReport'])
        ->name('reports.biomasas');
    Route::get('/reports/biomasas/export-excel', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'biomasasManagementExportExcel'])
        ->name('reports.biomasas.export-excel');
    Route::get('/reports/biomasas/export-pdf', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'biomasasManagementExportPdf'])
        ->name('reports.biomasas.export-pdf');

    Route::get('/reports/simulations', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'simulationsEffectivenessReport'])
        ->name('reports.simulations');
    Route::get('/reports/simulations/export-excel', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'simulationsEffectivenessExportExcel'])
        ->name('reports.simulations.export-excel');
    Route::get('/reports/simulations/export-pdf', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'simulationsEffectivenessExportPdf'])
        ->name('reports.simulations.export-pdf');

    Route::get('/reports/predictions', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'predictionsReport'])
        ->name('reports.predictions');
    Route::get('/reports/predictions/export-excel', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'predictionsReportExportExcel'])
        ->name('reports.predictions.export-excel');
    Route::get('/reports/predictions/export-pdf', [\Modules\Incendios\Http\Controllers\DashboardController::class, 'predictionsReportExportPdf'])
        ->name('reports.predictions.export-pdf');

    // Test endpoint to preview OpenWeather and FIRMS data
    Route::get('/test', [\Modules\Incendios\Http\Controllers\TestController::class, 'index'])
        ->name('test.index');

    // Debug endpoint para ver biomasas
    Route::get('/debug/biomasas', function () {
        $biomasas = \Modules\Incendios\Models\Biomasa::with('tipoBiomasa')->get();

        return response()->json([
            'total' => $biomasas->count(),
            'aprobadas' => $biomasas->where('estado', 'aprobada')->count(),
            'biomasas' => $biomasas->map(function ($b) {
                return [
                    'id' => $b->id,
                    'tipo' => $b->tipoBiomasa->tipo_biomasa ?? 'N/A',
                    'estado' => $b->estado,
                    'coordenadas' => $b->coordenadas,
                    'coordenadas_type' => gettype($b->coordenadas),
                    'area_m2' => $b->area_m2,
                ];
            }),
        ]);
    });

    Route::get('/home', function () {
        return redirect()->route('incendios.dashboard');
    });

    // Integración central: un solo login; el rol de trabajo se elige con la barra de contexto (sesión), no con Spatie en estas rutas.
    // ============================================
    // BIOMASAS
    // ============================================
    Route::get('biomasas/test-create', function () {
        $tipoBiomasas = \Modules\Incendios\Models\TipoBiomasa::all();

        return view('biomasa.test-create', compact('tipoBiomasas'));
    })->name('biomasas.test-create');

    Route::resource('biomasas', Modules\Incendios\Http\Controllers\BiomasaController::class);

    // ============================================
    // SIMULADOR, PREDICCIONES (lectura / PDFs)
    // ============================================
    Route::get('simulaciones/simulator', [Modules\Incendios\Http\Controllers\SimulacioneController::class, 'simulator'])
        ->name('simulaciones.simulator');
    Route::get('simulaciones/history-public', [Modules\Incendios\Http\Controllers\SimulacioneController::class, 'getHistory'])
        ->name('simulaciones.history.public');

    Route::get('predictions', [Modules\Incendios\Http\Controllers\PredictionController::class, 'index'])
        ->name('predictions.index');
    Route::get('predictions/create', [Modules\Incendios\Http\Controllers\PredictionController::class, 'create'])
        ->name('predictions.create');
    Route::get('predictions/{prediction}', [Modules\Incendios\Http\Controllers\PredictionController::class, 'show'])
        ->name('predictions.show');
    Route::get('predictions/{prediction}/pdf', [Modules\Incendios\Http\Controllers\PredictionController::class, 'showPdf'])
        ->name('predictions.pdf');
    Route::get('simulaciones/{simulacione}/pdf', [Modules\Incendios\Http\Controllers\SimulacioneController::class, 'showPdf'])
        ->name('simulaciones.pdf');

    // ============================================
    // CRUD / administración (misma política: usuario autenticado en el main)
    // ============================================
    Route::post('biomasas/{id}/aprobar', [Modules\Incendios\Http\Controllers\BiomasaController::class, 'aprobar'])
        ->name('biomasas.aprobar');
    Route::post('biomasas/{id}/rechazar', [Modules\Incendios\Http\Controllers\BiomasaController::class, 'rechazar'])
        ->name('biomasas.rechazar');

    Route::resource('tipo-biomasas', Modules\Incendios\Http\Controllers\TipoBiomasaController::class);

    Route::post('simulaciones/save-simulation', [Modules\Incendios\Http\Controllers\SimulacioneController::class, 'saveSimulation'])
        ->name('simulaciones.save');
    Route::get('simulaciones/history', [Modules\Incendios\Http\Controllers\SimulacioneController::class, 'getHistory'])
        ->name('simulaciones.history');
    Route::delete('simulaciones/delete/{id}', [Modules\Incendios\Http\Controllers\SimulacioneController::class, 'deleteSimulation'])
        ->name('simulaciones.delete');
    Route::resource('simulaciones', Modules\Incendios\Http\Controllers\SimulacioneController::class);

    Route::resource('focos-incendios', Modules\Incendios\Http\Controllers\FocosIncendioController::class);
    Route::post('focos-incendios/import/firms', [Modules\Incendios\Http\Controllers\FocosIncendioController::class, 'importFromFirms'])
        ->name('focos-incendios.import-firms');

    Route::post('predictions', [Modules\Incendios\Http\Controllers\PredictionController::class, 'store'])
        ->name('predictions.store');
    Route::get('predictions/{prediction}/edit', [Modules\Incendios\Http\Controllers\PredictionController::class, 'edit'])
        ->name('predictions.edit');
    Route::patch('predictions/{prediction}', [Modules\Incendios\Http\Controllers\PredictionController::class, 'update'])
        ->name('predictions.update');
    Route::delete('predictions/{prediction}', [Modules\Incendios\Http\Controllers\PredictionController::class, 'destroy'])
        ->name('predictions.destroy');
});

// Gestión de usuarios/voluntarios/helpdesk: centralizada en el módulo principal (transparencia).
