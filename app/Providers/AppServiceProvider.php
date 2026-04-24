<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::addNamespace('adminlte', resource_path('views/vendor/adminlte'));
        View::addNamespace('inventario', base_path('modulos/donacion-recepcion-inventario-main/resources/views'));
    }
}
