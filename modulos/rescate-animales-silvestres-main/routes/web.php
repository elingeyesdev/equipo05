<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Rescate\Http\Controllers\AnimalConditionController;
use Modules\Rescate\Http\Controllers\AnimalController;
use Modules\Rescate\Http\Controllers\AnimalFileController;
use Modules\Rescate\Http\Controllers\AnimalHistoryController;
use Modules\Rescate\Http\Controllers\AnimalStatusController;
use Modules\Rescate\Http\Controllers\CareController;
use Modules\Rescate\Http\Controllers\CareFeedingController;
use Modules\Rescate\Http\Controllers\CareTypeController;
use Modules\Rescate\Http\Controllers\CenterController;
use Modules\Rescate\Http\Controllers\ContactMessageController;
use Modules\Rescate\Http\Controllers\FeedingFrequencyController;
use Modules\Rescate\Http\Controllers\FeedingPortionController;
use Modules\Rescate\Http\Controllers\FeedingTypeController;
use Modules\Rescate\Http\Controllers\IncidentTypeController;
use Modules\Rescate\Http\Controllers\MedicalEvaluationController;
use Modules\Rescate\Http\Controllers\PersonController;
use Modules\Rescate\Http\Controllers\ProfileController;
use Modules\Rescate\Http\Controllers\QuickReportController;
use Modules\Rescate\Http\Controllers\ReleaseController;
use Modules\Rescate\Http\Controllers\ReportController;
use Modules\Rescate\Http\Controllers\ReportsController;
use Modules\Rescate\Http\Controllers\RescuerController;
use Modules\Rescate\Http\Controllers\SpeciesController;
use Modules\Rescate\Http\Controllers\Transactions\AnimalCareTransactionalController;
use Modules\Rescate\Http\Controllers\Transactions\AnimalFeedingTransactionalController;
use Modules\Rescate\Http\Controllers\Transactions\AnimalMedicalEvaluationTransactionalController;
use Modules\Rescate\Http\Controllers\Transactions\AnimalTransactionalController;
use Modules\Rescate\Http\Controllers\TransferController;
use Modules\Rescate\Http\Controllers\TreatmentTypeController;
use Modules\Rescate\Http\Controllers\UserController;
use Modules\Rescate\Http\Controllers\VeterinarianController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('rescate.home');
    }

    return redirect()->route('rescate.landing');
})->withoutMiddleware(['auth']);

// Authentication is centralized in the main application.

// Ruta para refrescar token CSRF (sin middleware CSRF)
Route::get('/refresh-csrf', function () {
    return response()->json([
        'token' => csrf_token(),
    ]);
})->middleware('web')->withoutMiddleware(['auth']);

Route::get('/landing', [Modules\Rescate\Http\Controllers\LandingController::class, 'index'])
    ->name('landing')
    ->withoutMiddleware(['auth']);

Route::get('/reporte-rapido', [QuickReportController::class, 'create'])
    ->name('reporte-rapido.create')
    ->withoutMiddleware(['auth']);
Route::post('/reporte-rapido', [QuickReportController::class, 'store'])
    ->name('reporte-rapido.store')
    ->middleware('throttle:10,1')
    ->withoutMiddleware(['auth']);
Route::get('/home', [Modules\Rescate\Http\Controllers\HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/dashboard/export-pdf', [Modules\Rescate\Http\Controllers\HomeController::class, 'exportPdf'])->middleware('auth')->name('dashboard.export-pdf');
Route::get('/dashboard/export-excel', [Modules\Rescate\Http\Controllers\HomeController::class, 'exportExcel'])->middleware('auth')->name('dashboard.export-excel');
Route::get('animal-histories/{animal_history}/pdf', [AnimalHistoryController::class, 'pdf'])->name('animal-histories.pdf')->middleware('auth');
Route::prefix('reports')->name('reports.')->group(function () {
    Route::put('{report}/approve', [ReportController::class, 'approve'])->name('approve')->middleware('auth');
    Route::get('claim', [ReportController::class, 'claim'])->name('claim')->withoutMiddleware(['auth']);
    Route::post('claim', [ReportController::class, 'claimStore'])->name('claim.store')->middleware('throttle:15,1')->withoutMiddleware(['auth']);
    Route::get('mapa-campo', [ReportController::class, 'mapaCampo'])->name('mapa-campo')->middleware('auth');
    Route::get('external-fire-reports', [ReportController::class, 'getExternalFireReportsApi'])->name('external-fire-reports.api')->middleware('auth');
    Route::get('external-fire-report/{externalId}', [ReportController::class, 'getExternalFireReportDetails'])->name('external-fire-report.details')->middleware('auth');
});

Route::resource('profile', ProfileController::class)->only(['index', 'update'])->middleware('auth');
Route::resource('contact-messages', ContactMessageController::class)->only(['store', 'update'])->middleware('auth');
Route::resource('centers', CenterController::class)->middleware('auth');
Route::resource('animals', AnimalController::class)->middleware('auth');
// Rutas de reports: create y store sin autenticación (para registro rápido desde landing)
Route::get('reports/create', [ReportController::class, 'create'])
    ->name('reports.create')
    ->withoutMiddleware(['auth']);
Route::post('reports', [ReportController::class, 'store'])
    ->name('reports.store')
    ->middleware('throttle:10,1')
    ->withoutMiddleware(['auth']);
// Resto de rutas de reports con autenticación
Route::resource('reports', ReportController::class)->except(['create', 'store'])->middleware('auth');
Route::resource('animal-statuses', AnimalStatusController::class)->middleware('auth');
Route::resource('care-types', CareTypeController::class)->middleware('auth');
Route::resource('cares', CareController::class)->middleware('auth');
Route::resource('animal-files', AnimalFileController::class)->middleware('auth');
Route::resource('people', PersonController::class);
Route::post('people/{person}/convert-to-encargado', [PersonController::class, 'convertToEncargado'])->name('people.convert-to-encargado')->middleware('auth');
Route::resource('species', SpeciesController::class)->middleware('auth');
Route::resource('releases', ReleaseController::class)->middleware('auth');
Route::put('rescuers/{rescuer}/approve', [RescuerController::class, 'approve'])->name('rescuers.approve')->middleware('auth');
Route::resource('rescuers', RescuerController::class)->middleware('auth');
Route::put('veterinarians/{veterinarian}/approve', [VeterinarianController::class, 'approve'])->name('veterinarians.approve')->middleware('auth');
Route::resource('veterinarians', VeterinarianController::class)->middleware('auth');
Route::resource('medical-evaluations', MedicalEvaluationController::class)->middleware('auth');
Route::resource('treatment-types', TreatmentTypeController::class)->middleware('auth');
Route::resource('transfers', TransferController::class)->middleware('auth');
Route::resource('care-feedings', CareFeedingController::class)->middleware('auth');
Route::resource('feeding-types', FeedingTypeController::class)->middleware('auth');
Route::resource('feeding-frequencies', FeedingFrequencyController::class)->middleware('auth');
Route::resource('feeding-portions', FeedingPortionController::class)->middleware('auth');
Route::resource('incident-types', IncidentTypeController::class)->middleware('auth');
Route::resource('animal-conditions', AnimalConditionController::class)->middleware('auth');
Route::resource('users', UserController::class)->middleware('auth');

// Transaccionales
Route::resource('animal-records', AnimalTransactionalController::class)->middleware('auth');
Route::resource('animal-feeding-records', AnimalFeedingTransactionalController::class)->middleware('auth');
Route::resource('medical-evaluation-transactions', AnimalMedicalEvaluationTransactionalController::class)->middleware('auth');
Route::resource('animal-care-records', AnimalCareTransactionalController::class)->middleware('auth');
Route::resource('animal-histories', AnimalHistoryController::class)->only(['index', 'show'])->middleware('auth');

Route::get('reportes', [ReportsController::class, 'index'])->name('reportes.index')->middleware('auth');
Route::get('reportes/exportar-pdf', [ReportsController::class, 'exportPdf'])->name('reportes.export-pdf')->middleware('auth');
Route::get('reportes/exportar-excel', [ReportsController::class, 'exportExcel'])->name('reportes.export-excel')->middleware('auth');

// ========== HELPDESK WIDGET ==========
// Ruta generada por: php artisan helpdeskwidget:install
Route::get('helpdesk', function () {
    return view('helpdesk');
})->name('helpdesk')->middleware('auth');
