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
        // Comandos de módulos (PSR-4 bajo Modules\*) no se auto-descubren como App\Console\Commands.
        $this->commands([
            \Modules\Incendios\Console\Commands\UpdateFirmsData::class,
            \Modules\Rescate\Console\Commands\ImportNasaFirms::class,
            \Modules\Rescate\Console\Commands\CheckFocosCalor::class,
            \Modules\Rescate\Console\Commands\TestEmail::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            if (! \Illuminate\Support\Facades\Auth::check()) {
                $view->with('contextModuleRoles', collect());
                $view->with('showModuleContextBar', false);
                $view->with('bodyModuleClass', '');

                return;
            }
            $path = request()->path();
            $inRescate = str_starts_with($path, 'rescate/modulo') || request()->routeIs('fusion.modulos.rescate');
            $inIncendios = str_starts_with($path, 'incendios/modulo') || request()->routeIs('fusion.modulos.incendios');
            if (! $inRescate && ! $inIncendios) {
                $view->with('contextModuleRoles', collect());
                $view->with('showModuleContextBar', false);
                $view->with('bodyModuleClass', '');

                return;
            }

            $view->with('bodyModuleClass', $inRescate ? 'module-rescate' : 'module-incendios');
            try {
                $view->with(
                    'contextModuleRoles',
                    \Spatie\Permission\Models\Role::query()->where('guard_name', 'web')->orderBy('name')->pluck('name')
                );
            } catch (\Throwable) {
                $view->with('contextModuleRoles', collect());
            }
            $view->with('showModuleContextBar', true);
            $view->with('moduleContextIsRescate', $inRescate);
            $view->with('moduleContextIsIncendios', $inIncendios);
        });

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
