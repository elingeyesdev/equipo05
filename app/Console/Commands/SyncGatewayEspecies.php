<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\RecursoExterno;

class SyncGatewayEspecies extends Command
{
    protected $signature = 'sync:gateway-especies';
    protected $description = 'Sincroniza listado de especies desde Gateway';

    public function handle(): int
    {
        $base = rtrim(config('services.externos.gateway_url'), '/');

        $listUrl = "{$base}/api/gateway/listado/especies";
        $resp = Http::timeout(25)->get($listUrl);

        if ($resp->failed()) {
            $this->error("Gateway listado especies falló: {$listUrl}");
            return self::FAILURE;
        }

        $especies = $resp->json()['especies'] ?? $resp->json()['data'] ?? [];

        foreach ($especies as $e) {
            $nombre = is_array($e) ? ($e['nombre'] ?? null) : $e;
            if (!$nombre) continue;

            RecursoExterno::updateOrCreate(
                ['tipo' => 'especie', 'identificador' => $nombre],
                ['datos_extra' => ['raw' => $e]]
            );
        }

        $this->info('Gateway especies OK.');
        return self::SUCCESS;
    }
}
