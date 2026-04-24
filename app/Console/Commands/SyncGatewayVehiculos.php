<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\RecursoExterno;

class SyncGatewayVehiculos extends Command
{
    protected $signature = 'sync:gateway-vehiculos';
    protected $description = 'Sincroniza listado de placas y cachea detalle de vehículos desde Gateway';

    public function handle(): int
    {
        $base = rtrim(config('services.externos.gateway_url'), '/');

        $listUrl = "{$base}/api/gateway/listado/placas";
        $resp = Http::timeout(25)->get($listUrl);

        if ($resp->failed()) {
            $this->error("Gateway listado placas falló: {$listUrl}");
            return self::FAILURE;
        }

        $placas = $resp->json()['placas'] ?? $resp->json()['data'] ?? [];

        foreach ($placas as $p) {
            $placa = is_array($p) ? ($p['placa'] ?? null) : $p;
            if (!$placa) continue;

            RecursoExterno::updateOrCreate(
                ['tipo' => 'vehiculo', 'identificador' => $placa],
                ['datos_extra' => ['raw' => $p]]
            );
        }

        $items = RecursoExterno::where('tipo', 'vehiculo')->get();

        foreach ($items as $item) {
            if ($item->response_detalle && $item->detalle_cached_at && $item->detalle_cached_at->diffInMinutes(now()) < 30) {
                continue;
            }

            $placa = $item->identificador;
            $detailUrl = "{$base}/api/gateway/trazabilidad/vehiculo/{$placa}";

            $detail = Http::timeout(30)->get($detailUrl);
            if ($detail->failed()) continue;

            $item->update([
                'response_detalle' => $detail->json(),
                'detalle_cached_at' => now(),
            ]);
        }

        $this->info('Gateway vehículos OK.');
        return self::SUCCESS;
    }
}
