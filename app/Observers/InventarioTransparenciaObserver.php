<?php

namespace App\Observers;

use App\Services\UnifiedDataSyncService;

/**
 * Tras cambios en inventario, actualiza espejo en transparencia.
 */
class InventarioTransparenciaObserver
{
    public function __construct(
        protected UnifiedDataSyncService $sync
    ) {}

    public function saved($model): void
    {
        $this->dispatchSync($model);
    }

    public function deleted($model): void
    {
        $this->dispatchSync($model);
    }

    protected function dispatchSync(object $model): void
    {
        if (! $this->sync->inventarioDisponible()) {
            return;
        }

        $class = $model::class;

        try {
            match ($class) {
                \Modules\Inventario\Models\Almacene::class,
                \Modules\Inventario\Models\Estante::class,
                \Modules\Inventario\Models\Espacio::class
                    => $this->sync->syncAlmacenesFromInventario(),

                \Modules\Inventario\Models\CategoriasProducto::class,
                \Modules\Inventario\Models\Producto::class
                    => $this->sync->syncCategoriasProductosFromInventario(),

                \Modules\Inventario\Models\Campana::class
                    => $this->sync->syncCampaniasFromInventario(),

                \Modules\Inventario\Models\UbicacionesDonacione::class,
                \Modules\Inventario\Models\DonacionDetalle::class,
                \Modules\Inventario\Models\Donacione::class,
                \Modules\Inventario\Models\Paquete::class
                    => $this->sync->syncTrazabilidadItemsFromInventario(),

                \Modules\Inventario\Models\DonacionesDinero::class
                    => $this->sync->syncDonacionesDineroFromInventario(),

                default => null,
            };
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('InventarioTransparenciaObserver: '.$e->getMessage());
        }
    }
}
