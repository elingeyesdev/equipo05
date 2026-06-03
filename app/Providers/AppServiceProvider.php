<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
            \App\Console\Commands\CleanRescateAnimalNames::class,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // AdminLTE/Bootstrap: evita la vista Tailwind por defecto (SVG gigantes sin estilos Tailwind).
        Paginator::defaultView('pagination::bootstrap-4');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-4');

        $this->registerInventarioTransparenciaSync();

        if (\App\Support\UnifiedPostgres::enabled() && config('cache.default') === 'database') {
            config(['cache.stores.database.connection' => 'core']);
        }

        if (\App\Support\UnifiedPostgres::enabled() && config('session.driver') === 'database') {
            config(['session.connection' => 'core']);
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

    private function registerInventarioTransparenciaSync(): void
    {
        $observer = \App\Observers\InventarioTransparenciaObserver::class;
        $models = [
            \Modules\Inventario\Models\Almacene::class,
            \Modules\Inventario\Models\Estante::class,
            \Modules\Inventario\Models\Espacio::class,
            \Modules\Inventario\Models\CategoriasProducto::class,
            \Modules\Inventario\Models\Producto::class,
            \Modules\Inventario\Models\Campana::class,
            \Modules\Inventario\Models\UbicacionesDonacione::class,
            \Modules\Inventario\Models\DonacionDetalle::class,
            \Modules\Inventario\Models\Donacione::class,
            \Modules\Inventario\Models\Paquete::class,
            \Modules\Inventario\Models\DonacionesDinero::class,
        ];

        foreach ($models as $model) {
            if (class_exists($model)) {
                $model::observe($observer);
            }
        }
    }
}
