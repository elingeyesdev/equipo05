<?php

use App\Http\Controllers\SeguimientoVoluntarios\ModuloController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [ModuloController::class, 'health']);
