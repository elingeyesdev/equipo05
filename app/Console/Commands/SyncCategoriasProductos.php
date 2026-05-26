<?php

namespace App\Console\Commands;

use App\Services\UnifiedDataSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Ext\ExtCategoriaProducto;
use App\Models\Ext\ExtProducto;

class SyncCategoriasProductos extends Command
{
    protected $signature = 'sync:categorias-productos';
    protected $description = 'Sincroniza categorías y productos desde API externa';

    public function handle(UnifiedDataSyncService $sync): int
    {
        $local = $sync->syncCategoriasProductosFromInventario();
        if ($local > 0) {
            $this->info("Categorías sincronizadas desde inventario local: {$local}");
        }

        $baseUrl = config('services.externos.donaciones_url');
        $url = "{$baseUrl}/api/categorias";

        $resp = Http::timeout(25)->get($url);

        if ($resp->failed()) {
            if ($local > 0) {
                $this->warn('API externa no disponible; se usaron categorías locales.');

                return self::SUCCESS;
            }
            $this->error("Error consumiendo {$url}");

            return self::FAILURE;
        }

        $json = $resp->json();
        $data = $json['data'] ?? [];

        foreach ($data as $cat) {
            $categoriaLocal = ExtCategoriaProducto::updateOrCreate(
                ['idexterno' => $cat['id_categoria']],
                ['nombre' => $cat['nombre']]
            );

            foreach (($cat['productos'] ?? []) as $prod) {
                ExtProducto::updateOrCreate(
                    ['idexterno' => $prod['id_producto']],
                    [
                        'categoriaid'   => $categoriaLocal->categoriaid,
                        'nombre'        => $prod['nombre'],
                        'unidad_medida' => $prod['unidad_medida'] ?? null,
                    ]
                );
            }
        }

        $this->info('Categorías/Productos sincronizados OK.');
        return self::SUCCESS;
    }
}
