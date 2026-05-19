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
        if (\App\Support\UnifiedPostgres::enabled() && config('cache.default') === 'database') {
            config(['cache.stores.database.connection' => 'core']);
        }

        View::composer(['layouts.app', 'fusion::layouts.app'], function ($view) {
            $path = request()->path();
            $moduleClass = match (true) {
                str_starts_with($path, 'inventario') => 'module-inventario',
                str_starts_with($path, 'incendios') => 'module-incendios',
                str_starts_with($path, 'rescate') => 'module-rescate',
                str_starts_with($path, 'logistica') => 'module-logistica',
                str_starts_with($path, 'seguimiento') => 'module-seguimiento',
                str_starts_with($path, 'cuadrillas') => 'module-cuadrillas',
                default => 'module-transparencia',
            };

            $view->with('bodyModuleClass', trim('platform-ui '.$moduleClass));

            if (! \Illuminate\Support\Facades\Auth::check()) {
                $view->with('contextModuleRoles', collect());
                $view->with('showModuleContextBar', false);

                return;
            }

            $inRescate = str_starts_with($path, 'rescate/modulo') || request()->routeIs('fusion.modulos.rescate');
            $inIncendios = str_starts_with($path, 'incendios/modulo') || request()->routeIs('fusion.modulos.incendios');

            if (! $inRescate && ! $inIncendios) {
                $view->with('contextModuleRoles', collect());
                $view->with('showModuleContextBar', false);

                return;
            }

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

        View::addNamespace('fusion', resource_path('views'));
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
