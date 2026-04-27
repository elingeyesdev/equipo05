<?php

use App\Http\Controllers\LogisticaTransportacion\ModuloController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ModuloController::class, 'index'])->name('dashboard');
