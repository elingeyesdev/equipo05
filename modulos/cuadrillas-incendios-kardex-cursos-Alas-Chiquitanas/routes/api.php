<?php

use App\Http\Controllers\CuadrillasIncendios\ModuloController;
use Illuminate\Support\Facades\Route;

Route::get('/health', [ModuloController::class, 'health']);
