<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\RecursoExterno;

class SyncGatewayPaquetes extends Command
{
    protected $signature = 'sync:gateway-paquetes';
    protected $description = 'Sincroniza listado de códigos (solicitudes) del Gateway y cachea detalle de paquetes';

    public function handle(): int
    {
        $base = rtrim(config('services.externos.gateway_url'), '/');

        // 1) Listado de códigos
        $listUrl = "{$base}/api/gateway/listado/codigos";
        $resp = Http::timeout(25)->get($listUrl);

        if ($resp->failed()) {
            $this->error("Gateway listado falló: {$listUrl}");
            return self::FAILURE;
        }

        $solicitudes = $resp->json()['solicitudes'] ?? [];

        foreach ($solicitudes as $s) {
            $codigo = $s['codigo_seguimiento'] ?? $s['codigo'] ?? null;
            if (!$codigo) continue;

            // Guardar "ligero" para el index
            $estado = $s['estado'] ?? ($s['aprobada'] ?? null);

            RecursoExterno::updateOrCreate(
                ['tipo' => 'solicitud', 'identificador' => $codigo],
                [
                    'gateway_id' => $s['id_solicitud'] ?? null,
                    'datos_extra' => [
                        'estado' => is_string($estado) ? $estado : (is_bool($estado) ? ($estado ? 'aprobada' : 'negada') : null),
                        'raw' => $s,
                    ],
                ]
            );
        }

        // 2) Cache detalle por código (opcional pero recomendado)
        $items = RecursoExterno::where('tipo', 'solicitud')->get();

        foreach ($items as $item) {
            // evitar saturar: si ya tiene detalle y fue cacheado hace < 10 min, saltar
            if ($item->response_detalle && $item->detalle_cached_at && $item->detalle_cached_at->diffInMinutes(now()) < 10) {
                continue;
            }

            $codigo = $item->identificador;
            $detailUrl = "{$base}/api/gateway/trazabilidad/paquete/{$codigo}";

            $detail = Http::timeout(30)->get($detailUrl);
            if ($detail->failed()) continue;

            $item->update([
                'response_detalle' => $detail->json(),
                'detalle_cached_at' => now(),
            ]);
        }

        $this->info('Gateway paquetes OK.');
        return self::SUCCESS;
    }
}
