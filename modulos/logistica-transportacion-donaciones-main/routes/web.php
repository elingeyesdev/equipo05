<?php

use App\Http\Controllers\LogisticaTransportacion\ModuloController;
use App\Http\Controllers\LogisticaTransportacion\SeccionesController;
use App\Support\FusionModuloAccess;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission.check:'.FusionModuloAccess::LOGISTICA_PERMISSIONS])->group(function () {
Route::get('/', [ModuloController::class, 'index'])->name('dashboard');
Route::get('/estadisticas', [ModuloController::class, 'index'])->name('estadisticas');

Route::get('/solicitud', [SeccionesController::class, 'solicitudes'])->name('solicitud');
Route::get('/solicitud/create', [SeccionesController::class, 'solicitudCreate'])->name('solicitud.create');
Route::post('/solicitud', [SeccionesController::class, 'solicitudStore'])->name('solicitud.store');
Route::get('/paquete', [SeccionesController::class, 'paquetes'])->name('paquete');
Route::get('/paquete/{id}/ficha', [SeccionesController::class, 'tracking'])->name('paquete.ficha');
Route::get('/seguimiento', fn () => redirect()->route('logistica.paquete'))->name('seguimiento');
Route::get('/seguimiento/tracking/{id}', [SeccionesController::class, 'tracking'])->name('seguimiento.tracking');
Route::get('/mapa', [SeccionesController::class, 'mapa'])->name('mapa');
Route::get('/flota', [SeccionesController::class, 'flota'])->name('flota');
Route::get('/configuracion', [SeccionesController::class, 'configuracion'])->name('configuracion');
Route::get('/vehiculo', fn () => redirect()->route('logistica.flota', ['tab' => 'vehiculos']))->name('vehiculo');

Route::get('/solicitante', [SeccionesController::class, 'show'])->defaults('seccion', 'solicitante')->name('solicitante');
Route::get('/destino', [SeccionesController::class, 'show'])->defaults('seccion', 'destino')->name('destino');
Route::get('/ubicacion', [SeccionesController::class, 'show'])->defaults('seccion', 'ubicacion')->name('ubicacion');
Route::get('/conductor', fn () => redirect()->route('logistica.flota', ['tab' => 'conductores']))->name('conductor');
Route::get('/marca', [SeccionesController::class, 'show'])->defaults('seccion', 'marca')->name('marca');
Route::get('/tipo-vehiculo', [SeccionesController::class, 'show'])->defaults('seccion', 'tipo-vehiculo')->name('tipo-vehiculo');

Route::get('/usuario', [SeccionesController::class, 'show'])->defaults('seccion', 'usuario')->name('usuario');
Route::get('/rol', [SeccionesController::class, 'show'])->defaults('seccion', 'rol')->name('rol');
Route::get('/estado', [SeccionesController::class, 'show'])->defaults('seccion', 'estado')->name('estado');
Route::get('/tipo-emergencia', [SeccionesController::class, 'show'])->defaults('seccion', 'tipo-emergencia')->name('tipo-emergencia');
Route::get('/tipo-licencia', [SeccionesController::class, 'show'])->defaults('seccion', 'tipo-licencia')->name('tipo-licencia');
Route::get('/reporte', [SeccionesController::class, 'show'])->defaults('seccion', 'reporte')->name('reporte');

Route::get('/galeria', fn () => redirect()->route('logistica.paquete', ['filtro' => 'galeria']))->name('galeria');
Route::get('/helpdesk', fn () => redirect()->route('logistica.solicitud', ['filtro' => 'soporte']))->name('helpdesk');

Route::prefix('crud/{seccion}')->group(function () {
    Route::get('/create', [SeccionesController::class, 'crudCreate'])->name('crud.create');
    Route::post('/', [SeccionesController::class, 'crudStore'])->name('crud.store');
    Route::get('/{id}/edit', [SeccionesController::class, 'crudEdit'])->name('crud.edit');
    Route::put('/{id}', [SeccionesController::class, 'crudUpdate'])->name('crud.update');
    Route::delete('/{id}', [SeccionesController::class, 'crudDestroy'])->name('crud.destroy');
});
});
