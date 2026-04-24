<?php

use Illuminate\Support\Facades\Route;

Route::prefix('inventario')
    ->group(function () {
        require base_path('modulos/donacion-recepcion-inventario-main/routes/api.php');
    });
