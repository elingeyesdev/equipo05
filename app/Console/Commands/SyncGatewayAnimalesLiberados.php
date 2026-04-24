<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\RecursoExterno;

class SyncGatewayAnimalesLiberados extends Command
{
    protected $signature = 'sync:gateway-animales-liberados';
    protected $description = 'Cachea animales liberados y genera detalle por especie (para showEspecie)';

    public function handle(): int
    {
        $base = rtrim(config('services.externos.gateway_url'), '/');

        // Este endpoint que tú confirmaste:
        $url = "{$base}/api/gateway/trazabilidad/animales/liberados";
        $resp = Http::timeout(35)->get($url);

        if ($resp->failed()) {
            $this->error("Gateway animales liberados falló: {$url}");
            return self::FAILURE;
        }

        $json = $resp->json();

        // Data real viene en: services.animales.data
        $items = $json['services']['animales']['data'] ?? [];
        if (!is_array($items)) $items = [];

        // Asegura que existan especies base (si no corriste SyncGatewayEspecies)
        foreach ($items as $it) {
            $especie = $it['especie']['nombre'] ?? null;
            if (!$especie) continue;

            RecursoExterno::updateOrCreate(
                ['tipo' => 'especie', 'identificador' => $especie],
                [
                    'datos_extra' => [
                        'total_animales' => null,
                    ]
                ]
            );
        }

        // Generar response_detalle por especie (lo que tu blade espera)
        $especies = RecursoExterno::where('tipo', 'especie')->get();

        foreach ($especies as $e) {
            $nombre = $e->identificador;

            $filtrados = array_values(array_filter($items, function ($it) use ($nombre) {
                return ($it['especie']['nombre'] ?? null) === $nombre;
            }));

            $e->update([
                'response_detalle' => [
                    'success' => true,
                    'tipo' => 'animales_liberados_por_especie',
                    'data' => [
                        'animales' => $filtrados,
                        'total' => count($filtrados),
                        'especie' => $nombre,
                    ],
                ],
                'datos_extra' => array_merge($e->datos_extra ?? [], [
                    'total_animales' => count($filtrados),
                ]),
                'detalle_cached_at' => now(),
            ]);
        }

        $this->info('Gateway animales liberados OK.');
        return self::SUCCESS;
    }
}
