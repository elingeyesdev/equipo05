<?php

namespace App\Console\Commands;

use App\Services\UnifiedDataSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Campania;

class SyncCampanias extends Command
{
    protected $signature = 'sync:campanias';
    protected $description = 'Sincroniza campañas desde API externa';

    public function handle(UnifiedDataSyncService $sync): int
    {
        $local = $sync->syncCampaniasFromInventario();
        if ($local > 0) {
            $this->info("Campañas sincronizadas desde inventario local: {$local}");
        }

        $baseUrl = config('services.externos.donaciones_url');

        $resp = Http::timeout(20)->get("{$baseUrl}/api/campanas");

        if ($resp->failed()) {
            if ($local > 0) {
                $this->warn("API externa no disponible; se usaron {$local} campañas locales.");

                return self::SUCCESS;
            }
            $this->error("No se pudo conectar a /api/campanas en {$baseUrl}");

            return self::FAILURE;
        }

        $campanasExternas = $resp->json();

        DB::transaction(function () use ($campanasExternas) {
            foreach ($campanasExternas as $c) {
                $campania = Campania::firstOrNew([
                    'idexterno' => $c['id_campana'],
                ]);

                $campania->titulo      = $c['nombre'];
                $campania->descripcion = $c['descripcion'];
                $campania->fechainicio = $c['fecha_inicio'];
                $campania->fechafin    = $c['fecha_fin'];
                $campania->imagenurl   = $c['imagen_banner'] ?? null;

                if (!$campania->exists) {
                    $campania->metarecaudacion = 0;
                    $campania->montorecaudado  = 0;
                    $campania->usuarioidcreador = 1;
                    $campania->activa = true;
                }

                $campania->save();
            }
        });

        $this->info('Campañas sincronizadas OK.');
        return self::SUCCESS;
    }
}
