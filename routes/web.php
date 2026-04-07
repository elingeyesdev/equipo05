<?php

use App\Http\Controllers\MonitoreoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MonitoreoController::class, 'index'])->name('home');

Route::get('/monitoreo', [MonitoreoController::class, 'index'])->name('monitoreo.index');
