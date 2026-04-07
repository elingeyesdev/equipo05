<?php

use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\VoluntarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MonitoreoController::class, 'index'])->name('home');

Route::get('/monitoreo', [MonitoreoController::class, 'index'])->name('monitoreo.index');
Route::get('/voluntarios', [VoluntarioController::class, 'index'])->name('voluntarios.index');
Route::get('/voluntarios/{id}', [VoluntarioController::class, 'show'])->name('voluntarios.show');
