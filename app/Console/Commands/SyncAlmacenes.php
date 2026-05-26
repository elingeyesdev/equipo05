<?php

namespace App\Console\Commands;

use App\Services\UnifiedDataSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Ext\ExtAlmacen;
use App\Models\Ext\ExtEstante;
use App\Models\Ext\ExtEspacio;

class SyncAlmacenes extends Command
{
    protected $signature = 'sync:almacenes';
    protected $description = 'Sincroniza almacenes/estantes/espacios desde API externa';

    public function handle(UnifiedDataSyncService $sync): int
    {
        $local = $sync->syncAlmacenesFromInventario();
        if ($local > 0) {
            $this->info("Almacenes sincronizados desde inventario local: {$local}");
        }

        $baseUrl = config('services.externos.donaciones_url');
        $url = "{$baseUrl}/api/almacenes-completo";

        $resp = Http::timeout(30)->get($url);

        if ($resp->failed()) {
            if ($local > 0) {
                $this->warn("API externa no disponible; se usaron {$local} almacenes locales.");

                return self::SUCCESS;
            }
            $this->error("Error consumiendo {$url}");

            return self::FAILURE;
        }

        $json = $resp->json();
        $data = $json['data'] ?? [];

        foreach ($data as $alm) {
            $almacenLocal = ExtAlmacen::updateOrCreate(
                ['idexterno' => $alm['id_almacen']],
                [
                    'nombre'    => $alm['nombre'],
                    'direccion' => $alm['direccion'] ?? null,
                ]
            );

            foreach (($alm['estantes'] ?? []) as $est) {
                $estanteLocal = ExtEstante::updateOrCreate(
                    ['idexterno' => $est['id_estante']],
                    [
                        'almacenid'      => $almacenLocal->almacenid,
                        'codigo_estante' => $est['codigo_estante'],
                    ]
                );

                foreach (($est['espacios'] ?? []) as $esp) {
                    ExtEspacio::updateOrCreate(
                        ['idexterno' => $esp['id_espacio']],
                        [
                            'estanteid'     => $estanteLocal->estanteid,
                            'codigo_espacio'=> $esp['codigo_espacio'],
                            'estado'        => $esp['estado'],
                        ]
                    );
                }
            }
        }

        $this->info('Almacenes sincronizados OK.');
        return self::SUCCESS;
    }
}
