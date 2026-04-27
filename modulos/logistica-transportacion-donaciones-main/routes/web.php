<?php

use App\Http\Controllers\LogisticaTransportacion\ModuloController;
use App\Http\Controllers\LogisticaTransportacion\SeccionesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ModuloController::class, 'index'])->name('dashboard');
Route::get('/estadisticas', [ModuloController::class, 'index'])->name('estadisticas');

Route::get('/solicitud', [SeccionesController::class, 'solicitudes'])->name('solicitud');
Route::get('/solicitud/create', [SeccionesController::class, 'solicitudCreate'])->name('solicitud.create');
Route::post('/solicitud', [SeccionesController::class, 'solicitudStore'])->name('solicitud.store');
Route::get('/paquete', [SeccionesController::class, 'paquetes'])->name('paquete');
Route::get('/seguimiento', [SeccionesController::class, 'seguimiento'])->name('seguimiento');
Route::get('/vehiculo', [SeccionesController::class, 'show'])->defaults('seccion', 'vehiculo')->name('vehiculo');

Route::get('/solicitante', [SeccionesController::class, 'show'])->defaults('seccion', 'solicitante')->name('solicitante');
Route::get('/destino', [SeccionesController::class, 'show'])->defaults('seccion', 'destino')->name('destino');
Route::get('/ubicacion', [SeccionesController::class, 'show'])->defaults('seccion', 'ubicacion')->name('ubicacion');
Route::get('/conductor', [SeccionesController::class, 'show'])->defaults('seccion', 'conductor')->name('conductor');
Route::get('/marca', [SeccionesController::class, 'show'])->defaults('seccion', 'marca')->name('marca');
Route::get('/tipo-vehiculo', [SeccionesController::class, 'show'])->defaults('seccion', 'tipo-vehiculo')->name('tipo-vehiculo');

Route::get('/usuario', [SeccionesController::class, 'show'])->defaults('seccion', 'usuario')->name('usuario');
Route::get('/rol', [SeccionesController::class, 'show'])->defaults('seccion', 'rol')->name('rol');
Route::get('/estado', [SeccionesController::class, 'show'])->defaults('seccion', 'estado')->name('estado');
Route::get('/tipo-emergencia', [SeccionesController::class, 'show'])->defaults('seccion', 'tipo-emergencia')->name('tipo-emergencia');
Route::get('/tipo-licencia', [SeccionesController::class, 'show'])->defaults('seccion', 'tipo-licencia')->name('tipo-licencia');
Route::get('/reporte', [SeccionesController::class, 'show'])->defaults('seccion', 'reporte')->name('reporte');

Route::get('/galeria', [SeccionesController::class, 'show'])->defaults('seccion', 'galeria')->name('galeria');
Route::get('/helpdesk', [SeccionesController::class, 'show'])->defaults('seccion', 'helpdesk')->name('helpdesk');
