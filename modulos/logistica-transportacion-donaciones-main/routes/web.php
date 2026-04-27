<?php

use App\Http\Controllers\LogisticaTransportacion\ModuloController;
use App\Http\Controllers\LogisticaTransportacion\SeccionesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ModuloController::class, 'index'])->name('dashboard');

Route::get('/solicitudes', [SeccionesController::class, 'solicitudes'])->name('solicitudes');
Route::get('/paquetes', [SeccionesController::class, 'paquetes'])->name('paquetes');
Route::get('/seguimiento', [SeccionesController::class, 'seguimiento'])->name('seguimiento');
Route::get('/solicitantes', [SeccionesController::class, 'show'])->defaults('seccion', 'solicitantes')->name('solicitantes');
Route::get('/destinos', [SeccionesController::class, 'show'])->defaults('seccion', 'destinos')->name('destinos');
Route::get('/ubicaciones', [SeccionesController::class, 'show'])->defaults('seccion', 'ubicaciones')->name('ubicaciones');
Route::get('/vehiculos', [SeccionesController::class, 'show'])->defaults('seccion', 'vehiculos')->name('vehiculos');
Route::get('/conductores', [SeccionesController::class, 'show'])->defaults('seccion', 'conductores')->name('conductores');
Route::get('/tipos-vehiculo', [SeccionesController::class, 'show'])->defaults('seccion', 'tipos-vehiculo')->name('tipos-vehiculo');
Route::get('/tipos-licencia', [SeccionesController::class, 'show'])->defaults('seccion', 'tipos-licencia')->name('tipos-licencia');
Route::get('/tipos-emergencia', [SeccionesController::class, 'show'])->defaults('seccion', 'tipos-emergencia')->name('tipos-emergencia');
Route::get('/marcas', [SeccionesController::class, 'show'])->defaults('seccion', 'marcas')->name('marcas');
Route::get('/reportes', [SeccionesController::class, 'show'])->defaults('seccion', 'reportes')->name('reportes');
Route::get('/usuarios', [SeccionesController::class, 'show'])->defaults('seccion', 'usuarios')->name('usuarios');
Route::get('/roles', [SeccionesController::class, 'show'])->defaults('seccion', 'roles')->name('roles');
Route::get('/estados', [SeccionesController::class, 'show'])->defaults('seccion', 'estados')->name('estados');
