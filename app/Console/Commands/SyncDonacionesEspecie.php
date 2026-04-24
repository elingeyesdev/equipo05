<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Campania;
use App\Models\TrazabilidadItem;
use App\Models\Ext\ExtProducto;
use App\Models\Ext\ExtAlmacen;
use App\Models\Ext\ExtEstante;
use App\Models\Ext\ExtEspacio;

class SyncDonacionesEspecie extends Command
{
    protected $signature = 'sync:donaciones-especie';
    protected $description = 'Sincroniza donaciones en especie + ubicaciones + referencia a paquete (si existe en payload)';

    public function handle(): int
    {
        // dependencias
        $this->call('sync:campanias');
        $this->call('sync:categorias-productos');
        $this->call('sync:almacenes');

        $baseUrl = config('services.externos.donaciones_url');

        $resp = Http::timeout(35)->get("{$baseUrl}/api/donaciones/especie");
        if ($resp->failed()) {
            $this->error('No se pudo conectar a /api/donaciones/especie');
            return self::FAILURE;
        }

        $donaciones = $resp->json();

        DB::transaction(function () use ($donaciones, $baseUrl) {
            foreach ($donaciones as $don) {

                $idDonacionExt = $don['id_donacion'];

                $detallesLista = [];
                foreach ($don['detalles'] ?? [] as $det) {
                    $detallesLista[$det['id_producto']] = $det;
                }

                $campaniaLocal = null;
                if (!empty($don['id_campana'])) {
                    $campaniaLocal = Campania::where('idexterno', $don['id_campana'])->first();
                }

                $nombreDonante = $don['donante']['nombre'] ?? null;

                $detalleResp = Http::timeout(35)->get("{$baseUrl}/api/donaciones/especie/{$idDonacionExt}/detalle");
                if ($detalleResp->failed()) continue;

                $detalleJson = $detalleResp->json();
                $data = $detalleJson['data'] ?? null;
                if (!$data || empty($data['detalles'])) continue;

                foreach ($data['detalles'] as $detDetalle) {
                    $productoExt = $detDetalle['producto'] ?? null;
                    if (!$productoExt) continue;

                    $idProductoExt = $productoExt['id_producto'];
                    $detalleBase = $detallesLista[$idProductoExt] ?? null;

                    $idDetalleExt    = $detalleBase['id_detalle'] ?? null;
                    $cantidadDonada  = $detalleBase['cantidad'] ?? $detDetalle['cantidad'] ?? 0;
                    $unidadMedida    = $detalleBase['unidad_medida'] ?? $detDetalle['unidad_medida'] ?? 'Unid';
                    $codigoUnico     = $detalleBase['codigo_unico'] ?? "DON{$idDonacionExt}-DET" . ($idDetalleExt ?? $idProductoExt);

                    $productoLocal = ExtProducto::where('idexterno', $idProductoExt)->first();

                    // si el payload ya trae paquetes, guardamos referencia (solo código)
                    $codigoPaquete = null;
                    if (!empty($detDetalle['paquetes']) && is_array($detDetalle['paquetes'])) {
                        $codigoPaquete = $detDetalle['paquetes'][0]['codigo_paquete'] ?? null;
                    }

                    $ubicaciones = $detDetalle['ubicaciones'] ?? [];

                    if (empty($ubicaciones)) {
                        $this->guardarItem(
                            $idDonacionExt, $idDetalleExt, $idProductoExt, $campaniaLocal, $don,
                            $codigoUnico, $productoLocal, $productoExt, $cantidadDonada, $unidadMedida,
                            $nombreDonante, null, null, null, null,
                            'Sin ubicación', null, $codigoPaquete
                        );
                        continue;
                    }

                    foreach ($ubicaciones as $ubic) {
                        $almacenData = $ubic['almacen'] ?? [];
                        $estanteData = $ubic['estante'] ?? [];
                        $espacioData = $ubic['espacio'] ?? [];

                        $almacenLocal = isset($almacenData['id_almacen'])
                            ? ExtAlmacen::where('idexterno', $almacenData['id_almacen'])->first()
                            : null;

                        $estanteLocal = isset($estanteData['id_estante'])
                            ? ExtEstante::where('idexterno', $estanteData['id_estante'])->first()
                            : null;

                        $espacioLocal = isset($espacioData['id_espacio'])
                            ? ExtEspacio::where('idexterno', $espacioData['id_espacio'])->first()
                            : null;

                        $ubicacionTexto = trim(
                            ($almacenData['nombre'] ?? '') . ' / ' .
                            ($estanteData['codigo_estante'] ?? '') . ' / ' .
                            ($espacioData['codigo_espacio'] ?? '')
                        );

                        $this->guardarItem(
                            $idDonacionExt, $idDetalleExt, $idProductoExt, $campaniaLocal, $don,
                            $codigoUnico, $productoLocal, $productoExt, $cantidadDonada, $unidadMedida,
                            $nombreDonante,
                            $almacenLocal, $estanteLocal, $espacioLocal,
                            $almacenData,
                            'En almacén',
                            $ubicacionTexto,
                            $codigoPaquete
                        );
                    }
                }
            }
        });

        $this->info('Donaciones en especie sincronizadas OK.');
        return self::SUCCESS;
    }

    private function guardarItem(
        $idDonExt, $idDetExt, $idProdExt,
        $campania, $don, $codigo,
        $prodLocal, $prodExt,
        $cant, $unidad, $donante,
        $alm, $est, $esp,
        $almData, $estado, $ubi,
        $codPaquete
    ): void {
        TrazabilidadItem::updateOrCreate(
            ['id_donacion_externa' => $idDonExt, 'id_detalle_externo' => $idDetExt ?? $idProdExt],
            [
                'campaniaid' => $campania?->campaniaid,
                'id_campana_externa' => $don['id_campana'] ?? null,
                'campania_nombre' => $don['campana']['nombre'] ?? null,
                'codigo_unico' => $codigo,
                'productoid' => $prodLocal?->productoid,
                'nombre_producto' => $prodExt['nombre'] ?? null,
                'categoria_producto' => $prodLocal?->categoria->nombre ?? null,
                'cantidad_donada' => $cant,
                'unidad_empaque' => $unidad,
                'fecha_donacion' => $don['fecha'] ?? null,
                'nombre_donante' => $donante,
                'almacenid' => $alm?->almacenid,
                'estanteid' => $est?->estanteid,
                'espacioid' => $esp?->espacioid,
                'almacen_nombre' => $almData['nombre'] ?? null,
                'estado_actual' => $estado,
                'ubicacion_actual' => $ubi,
                'codigo_paquete' => $codPaquete,
                'fecha_ultima_actualizacion' => now(),
            ]
        );
    }
}
