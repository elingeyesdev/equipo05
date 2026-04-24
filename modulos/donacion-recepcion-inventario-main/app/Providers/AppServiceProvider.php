<?php

namespace Modules\Inventario\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Spatie Laravel Permission ahora maneja todos los permisos automáticamente
        // Los Gates se registran automáticamente desde la tabla 'permissions'
        // Ya no es necesario definirlos manualmente aquí
    }
}




