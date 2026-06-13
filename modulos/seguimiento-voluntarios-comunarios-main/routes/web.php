<?php

use App\Http\Controllers\SeguimientoVoluntarios\ModuloController;
use App\Http\Controllers\SeguimientoVoluntarios\SeccionesController;
use App\Support\FusionModuloAccess;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission.check:'.FusionModuloAccess::VOLUNTARIOS_PERMISSIONS.'|'.FusionModuloAccess::VOLUNTARIO_PANEL_PERMISSIONS])->group(function () {
Route::get('/', [ModuloController::class, 'index'])->name('dashboard');
Route::get('/estadisticas', [ModuloController::class, 'index'])->name('estadisticas');

Route::get('/voluntarios', [SeccionesController::class, 'show'])->defaults('seccion', 'voluntarios')->name('voluntarios');
Route::get('/voluntarios-inactivos', [SeccionesController::class, 'show'])->defaults('seccion', 'voluntarios-inactivos')->name('voluntarios-inactivos');
Route::get('/evaluacion', [SeccionesController::class, 'show'])->defaults('seccion', 'evaluacion')->name('evaluacion');
Route::get('/evaluacion-pruebas', [SeccionesController::class, 'show'])->defaults('seccion', 'evaluacion-pruebas')->name('evaluacion-pruebas');
Route::get('/capacitaciones', [SeccionesController::class, 'show'])->defaults('seccion', 'capacitaciones')->name('capacitaciones');
Route::get('/necesidades', [SeccionesController::class, 'show'])->defaults('seccion', 'necesidades')->name('necesidades');
Route::get('/ayudas-solicitadas', [SeccionesController::class, 'show'])->defaults('seccion', 'ayudas-solicitadas')->name('ayudas-solicitadas');
Route::get('/administradores', [SeccionesController::class, 'show'])->defaults('seccion', 'administradores')->name('administradores');
Route::get('/universidades', [SeccionesController::class, 'show'])->defaults('seccion', 'universidades')->name('universidades');
Route::get('/chat-consulta', [SeccionesController::class, 'show'])->defaults('seccion', 'chat-consulta')->name('chat-consulta');
Route::post('/chat-consulta/mensaje', [SeccionesController::class, 'chatEnviar'])->name('chat.enviar');
Route::get('/helpdesk', [SeccionesController::class, 'show'])->defaults('seccion', 'helpdesk')->name('helpdesk');

Route::prefix('crud/{seccion}')->group(function () {
    Route::get('/create', [SeccionesController::class, 'crudCreate'])->name('crud.create');
    Route::post('/', [SeccionesController::class, 'crudStore'])->name('crud.store');
    Route::get('/{id}/edit', [SeccionesController::class, 'crudEdit'])->name('crud.edit');
    Route::put('/{id}', [SeccionesController::class, 'crudUpdate'])->name('crud.update');
    Route::delete('/{id}', [SeccionesController::class, 'crudDestroy'])->name('crud.destroy');
});
});
