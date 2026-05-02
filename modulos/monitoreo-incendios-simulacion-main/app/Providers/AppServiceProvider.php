<?php

namespace Modules\Incendios\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\Incendios\Models\User;
use Modules\Incendios\Observers\UserObserver;

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
        // Register User Observer to auto-assign 'voluntario' role
        User::observe(UserObserver::class);
        
        // Integración: un solo login; no ocultar menús admin por Spatie en este módulo.
        Gate::define('viewAdmin', function ($user) {
            return $user !== null;
        });
    }
}
