<?php

use App\Http\Controllers\HistorialController;
use App\Http\Controllers\IncendioController;
use App\Http\Controllers\MonitoreoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MonitoreoController::class, 'index'])->name('home');

Route::get('/monitoreo', [MonitoreoController::class, 'index'])->name('monitoreo.index');

Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');

Route::get('/incendios/crear', [IncendioController::class, 'create'])->name('incendios.create');
Route::post('/incendios', [IncendioController::class, 'store'])->name('incendios.store');
Route::get('/incendios/{incendio}/editar', [IncendioController::class, 'edit'])->name('incendios.edit');
Route::put('/incendios/{incendio}', [IncendioController::class, 'update'])->name('incendios.update');
Route::delete('/incendios/{incendio}', [IncendioController::class, 'destroy'])->name('incendios.destroy');
