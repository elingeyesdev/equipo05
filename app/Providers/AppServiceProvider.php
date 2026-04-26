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
        View::addNamespace('incendios', base_path('modulos/monitoreo-incendios-simulacion-main/resources/views'));
        View::addNamespace('rescate', base_path('modulos/rescate-animales-silvestres-main/resources/views'));

        // Permite resolver vistas legacy de modulos (view('dashboard'), view('home'), etc.)
        // sin romper el sistema principal.
        View::addLocation(base_path('modulos/monitoreo-incendios-simulacion-main/resources/views'));
        View::addLocation(base_path('modulos/rescate-animales-silvestres-main/resources/views'));

    }
}
