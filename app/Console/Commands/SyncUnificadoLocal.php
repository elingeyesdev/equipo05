<?php

namespace App\Console\Commands;

use App\Services\UnifiedDataSyncService;
use Illuminate\Console\Command;

class SyncUnificadoLocal extends Command
{
    protected $signature = 'sync:unificado-local {--only= : almacenes,categorias,campanias,trazabilidad,donaciones}';

    protected $description = 'Sincroniza inventario local → transparencia (ext_* y trazabilidad_items)';

    public function handle(UnifiedDataSyncService $sync): int
    {
        if (! $sync->inventarioDisponible()) {
            $this->error('No hay conexión o tablas del módulo inventario.');

            return self::FAILURE;
        }

        $only = $this->option('only');

        if ($only) {
            $stats = match ($only) {
                'almacenes' => ['almacenes' => $sync->syncAlmacenesFromInventario()],
                'categorias' => ['categorias' => $sync->syncCategoriasProductosFromInventario()],
                'campanias' => ['campanias' => $sync->syncCampaniasFromInventario()],
                'trazabilidad' => ['trazabilidad' => $sync->syncTrazabilidadItemsFromInventario()],
                'donaciones' => ['donaciones_dinero' => $sync->syncDonacionesDineroFromInventario()],
                default => null,
            };

            if ($stats === null) {
                $this->error('Opción --only inválida. Use: almacenes, categorias, campanias, trazabilidad, donaciones');

                return self::FAILURE;
            }
        } else {
            $stats = $sync->syncAllFromInventario();
        }

        foreach ($stats as $key => $value) {
            $this->info("✔ {$key}: {$value}");
        }

        return self::SUCCESS;
    }
}
