<?php

use App\Http\Controllers\CuadrillasIncendios\ModuloController;
use App\Http\Controllers\CuadrillasIncendios\SeccionesController;
use App\Support\FusionModuloAccess;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission.check:'.FusionModuloAccess::CUADRILLAS_PERMISSIONS])->group(function () {
Route::get('/', [ModuloController::class, 'index'])->name('dashboard');
Route::get('/estadisticas', [ModuloController::class, 'index'])->name('estadisticas');

Route::get('/reportes', [SeccionesController::class, 'show'])->defaults('seccion', 'reportes')->name('reportes');
Route::get('/focos-calor', [SeccionesController::class, 'show'])->defaults('seccion', 'focos-calor')->name('focos-calor');
Route::get('/noticias', [SeccionesController::class, 'show'])->defaults('seccion', 'noticias')->name('noticias');
Route::get('/cursos', [SeccionesController::class, 'show'])->defaults('seccion', 'cursos')->name('cursos');

Route::prefix('crud/{seccion}')->group(function () {
    Route::get('/create', [SeccionesController::class, 'crudCreate'])->name('crud.create');
    Route::post('/', [SeccionesController::class, 'crudStore'])->name('crud.store');
    Route::get('/{id}/edit', [SeccionesController::class, 'crudEdit'])->name('crud.edit');
    Route::put('/{id}', [SeccionesController::class, 'crudUpdate'])->name('crud.update');
    Route::delete('/{id}', [SeccionesController::class, 'crudDestroy'])->name('crud.destroy');
});

Route::post('/noticias/scrape', [SeccionesController::class, 'scrapeNoticias'])->name('noticias.scrape');
});
