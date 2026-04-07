<?php

use App\Http\Controllers\HistorialIncendioController;
use App\Http\Controllers\IncendioController;
use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\VoluntarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MonitoreoController::class, 'index'])->name('home');

Route::get('/monitoreo', [MonitoreoController::class, 'index'])->name('monitoreo.index');
Route::get('/incendios/crear', [IncendioController::class, 'create'])->name('incendios.create');
Route::post('/incendios', [IncendioController::class, 'store'])->name('incendios.store');
Route::get('/incendios/{incendio}/editar', [IncendioController::class, 'edit'])->name('incendios.edit');
Route::put('/incendios/{incendio}', [IncendioController::class, 'update'])->name('incendios.update');
Route::delete('/incendios/{incendio}', [IncendioController::class, 'destroy'])->name('incendios.destroy');

Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
Route::patch('/notificaciones/{notificacion}/leida', [NotificacionController::class, 'marcarComoLeida'])
    ->name('notificaciones.marcar');
Route::patch('/notificaciones/leidas', [NotificacionController::class, 'marcarTodasComoLeidas'])
    ->name('notificaciones.marcar-todas');

Route::get('/historial', [HistorialIncendioController::class, 'index'])->name('historial.index');
Route::get('/voluntarios', [VoluntarioController::class, 'index'])->name('voluntarios.index');
Route::get('/voluntarios/{id}', [VoluntarioController::class, 'show'])->name('voluntarios.show');
