<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Observers\UserObserver;

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
        
        // Gate para verificar si el usuario es administrador
        // Usa Spatie roles internamente
        Gate::define('viewAdmin', function ($user) {
            return $user->hasRole('administrador');
        });
    }
}
