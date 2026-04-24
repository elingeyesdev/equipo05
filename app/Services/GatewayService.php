<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GatewayService
{
    private string $base = 'http://gatealas.dasalas.shop/api/gateway';

    /**
     * SINCRONIZA LISTADOS (para llenar recursos_externos)
     * - solicitudes:  /listado/codigos
     * - vehiculos:    /listado/placas
     * - especies:     /listado/especies
     */
    public function syncListados(): array
    {
        $out = [
            'solicitudes' => 0,
            'vehiculos' => 0,
            'especies' => 0,
            'errors' => [],
        ];

        // 1) Solicitudes (codigos)
        try {
            $r = Http::timeout(10)->get("{$this->base}/listado/codigos");
            if ($r->successful()) {
                $json = $r->json();

                $items = $json['solicitudes'] ?? [];
                foreach ($items as $it) {
                    $codigo = $it['codigo_seguimiento'] ?? null;
                    if (!$codigo) continue;

                    // ExtSolicitud extiende RecursoExterno y fuerza tipo=solicitud
                    \App\Models\Ext\ExtSolicitud::updateOrCreate(
                        ['identificador' => $codigo],
                        [
                            'gateway_id' => $it['id_solicitud'] ?? null,
                            'datos_extra' => [
                                'estado' => $it['estado'] ?? null,
                            ],
                        ]
                    );
                    $out['solicitudes']++;
                }
            } else {
                $out['errors'][] = "listado/codigos -> HTTP ".$r->status();
            }
        } catch (\Throwable $e) {
            $out['errors'][] = "listado/codigos -> ".$e->getMessage();
            Log::error("Gateway listado/codigos error: ".$e->getMessage());
        }

        // 2) Vehiculos (placas)
        try {
            $r = Http::timeout(10)->get("{$this->base}/listado/placas");
            if ($r->successful()) {
                $json = $r->json();
                $items = $json['vehiculos'] ?? [];
                foreach ($items as $it) {
                    $placa = $it['placa'] ?? null;
                    if (!$placa) continue;

                    \App\Models\Ext\ExtVehiculo::updateOrCreate(
                        ['identificador' => $placa],
                        [
                            'gateway_id' => $it['id_vehiculo'] ?? null,
                            'datos_extra' => [],
                        ]
                    );
                    $out['vehiculos']++;
                }
            } else {
                $out['errors'][] = "listado/placas -> HTTP ".$r->status();
            }
        } catch (\Throwable $e) {
            $out['errors'][] = "listado/placas -> ".$e->getMessage();
            Log::error("Gateway listado/placas error: ".$e->getMessage());
        }

        // 3) Especies (array directo)
        try {
            $r = Http::timeout(10)->get("{$this->base}/listado/especies");
            if ($r->successful()) {
                $items = $r->json(); // viene como array directo [{id,nombre}, ...]
                if (is_array($items)) {
                    foreach ($items as $it) {
                        $nombre = $it['nombre'] ?? null;
                        if (!$nombre) continue;

                        \App\Models\Ext\ExtEspecie::updateOrCreate(
                            ['identificador' => $nombre],
                            [
                                'gateway_id' => $it['id'] ?? null,
                                'datos_extra' => [],
                            ]
                        );
                        $out['especies']++;
                    }
                }
            } else {
                $out['errors'][] = "listado/especies -> HTTP ".$r->status();
            }
        } catch (\Throwable $e) {
            $out['errors'][] = "listado/especies -> ".$e->getMessage();
            Log::error("Gateway listado/especies error: ".$e->getMessage());
        }

        return $out;
    }

    /**
     * DETALLE (cache resiliente)
     */
    public function obtenerDetalle(string $claseModelo, string $identificador, string $urlApi, array $attrsCreate = [])
    {
        // Importante: crear con identificador + attrs (tipo, etc)
        $recurso = $claseModelo::firstOrCreate(
            ['identificador' => $identificador],
            $attrsCreate
        );

        if (
            $recurso->response_detalle &&
            $recurso->detalle_cached_at &&
            $recurso->detalle_cached_at->diffInMinutes(now()) < 5
        ) {
            return $recurso;
        }

        try {
            $response = Http::timeout(10)->get($urlApi);

            if ($response->successful()) {
                $recurso->update([
                    'response_detalle' => $response->json(),
                    'detalle_cached_at' => now(),
                ]);
            } else {
                Log::warning("Gateway detalle HTTP {$response->status()} url={$urlApi}");
            }
        } catch (\Throwable $e) {
            Log::error("Fallo Gateway detalle ({$identificador}): ".$e->getMessage());
        }

        return $recurso;
    }
}
