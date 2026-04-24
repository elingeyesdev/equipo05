<?php

namespace App\Http\Controllers\Ext;

use App\Http\Controllers\Controller;
use App\Models\Campania;
use App\Models\TrazabilidadItem;
use App\Models\Ext\ExtProducto;
use App\Models\Ext\ExtAlmacen;
use App\Models\Ext\ExtEstante;
use App\Models\Ext\ExtEspacio;
use App\Models\Ext\ExtPaquete; // <--- Importamos el nuevo modelo
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrazabilidadSyncController extends Controller
{
    protected string $baseUrl;
    protected string $gatewayUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.externos.donaciones_url'); // Corregido para usar la config correcta
        $this->gatewayUrl = config('services.externos.gateway_url');
    }

    public function syncEspecie()
    {
        // 1. Traer todas las donaciones
        $resp = Http::get("{$this->baseUrl}/api/donaciones/especie");

        if ($resp->failed()) {
            return back()->withErrors('No se pudo conectar a la API de donaciones.');
        }

        $donaciones = $resp->json();

        DB::transaction(function () use ($donaciones) {
            foreach ($donaciones as $don) {
                
                $idDonacionExt = $don['id_donacion'];
                
                // Preparar mapa de detalles
                $detallesLista = [];
                foreach ($don['detalles'] ?? [] as $det) {
                    $detallesLista[$det['id_producto']] = $det;
                }

                // Campaña local
                $campaniaLocal = null;
                if (!empty($don['id_campana'])) {
                    $campaniaLocal = Campania::where('idexterno', $don['id_campana'])->first();
                }
                
                $nombreDonante = $don['donante']['nombre'] ?? null;

                // 2. Detalle profundo (ubicaciones y paquetes)
                $detalleResp = Http::get("{$this->baseUrl}/api/donaciones/especie/{$idDonacionExt}/detalle");
                if ($detalleResp->failed()) continue;

                $detalleJson = $detalleResp->json();
                $data = $detalleJson['data'] ?? null;
                if (!$data || empty($data['detalles'])) continue;

                foreach ($data['detalles'] as $detDetalle) {
                    $productoExt = $detDetalle['producto'] ?? null;
                    if (!$productoExt) continue;

                    $idProductoExt = $productoExt['id_producto'];
                    $detalleBase = $detallesLista[$idProductoExt] ?? null;

                    // Datos básicos
                    $idDetalleExt = $detalleBase['id_detalle'] ?? null;
                    $cantidadDonada = $detalleBase['cantidad'] ?? $detDetalle['cantidad'] ?? 0;
                    $unidadMedida = $detalleBase['unidad_medida'] ?? $detDetalle['unidad_medida'] ?? 'Unid';
                    
                    // Generar código único
                    $codigoUnico = $detalleBase['codigo_unico'] ?? "DON{$idDonacionExt}-DET" . ($idDetalleExt ?? $idProductoExt);

                    // Producto local
                    $productoLocal = ExtProducto::where('idexterno', $idProductoExt)->first();

                    // --- LOGICA DE PAQUETES (GATEWAY) ---
                    $codigoPaquete = null;
                    if (!empty($detDetalle['paquetes']) && is_array($detDetalle['paquetes'])) {
                        $codigoPaquete = $detDetalle['paquetes'][0]['codigo_paquete'] ?? null;
                        
                        // Si hay código, guardamos la info del Gateway LOCALMENTE
                        if ($codigoPaquete) {
                            $this->syncPaqueteGateway($codigoPaquete);
                        }
                    }

                    // Ubicaciones
                    $ubicaciones = $detDetalle['ubicaciones'] ?? [];

                    if (empty($ubicaciones)) {
                        $this->guardarItem($idDonacionExt, $idDetalleExt, $idProductoExt, $campaniaLocal, $don, $codigoUnico, $productoLocal, $productoExt, $cantidadDonada, $unidadMedida, $nombreDonante, null, null, null, null, 'Sin ubicación', null, $codigoPaquete);
                        continue;
                    }

                    foreach ($ubicaciones as $ubic) {
                        $almacenData = $ubic['almacen'] ?? [];
                        $estanteData = $ubic['estante'] ?? [];
                        $espacioData = $ubic['espacio'] ?? [];

                        $almacenLocal = isset($almacenData['id_almacen']) ? ExtAlmacen::where('idexterno', $almacenData['id_almacen'])->first() : null;
                        $estanteLocal = isset($estanteData['id_estante']) ? ExtEstante::where('idexterno', $estanteData['id_estante'])->first() : null;
                        $espacioLocal = isset($espacioData['id_espacio']) ? ExtEspacio::where('idexterno', $espacioData['id_espacio'])->first() : null;

                        $ubicacionTexto = trim(($almacenData['nombre'] ?? '') . ' / ' . ($estanteData['codigo_estante'] ?? '') . ' / ' . ($espacioData['codigo_espacio'] ?? ''));

                        $this->guardarItem($idDonacionExt, $idDetalleExt, $idProductoExt, $campaniaLocal, $don, $codigoUnico, $productoLocal, $productoExt, $cantidadDonada, $unidadMedida, $nombreDonante, $almacenLocal, $estanteLocal, $espacioLocal, $almacenData, 'En almacén', $ubicacionTexto, $codigoPaquete);
                    }
                }
            }
        });

        return back()->with('success', 'Sincronización completa (Donaciones y Paquetes Gateway).');
    }

    // Método auxiliar para guardar/actualizar el paquete desde el Gateway
    private function syncPaqueteGateway($codigo)
    {
        // Verificar si ya existe y si se actualizó hace poco (ej: 1 hora) para no saturar
        $paqueteLocal = ExtPaquete::where('codigo_paquete', $codigo)->first();
        
        if ($paqueteLocal && $paqueteLocal->ultimo_sync->diffInHours(now()) < 1) {
            return; // Ya está actualizado
        }

        try {
            $response = Http::timeout(2)->get("{$this->gatewayUrl}/api/gateway/trazabilidad/paquete/{$codigo}");
            
            if ($response->successful()) {
                $data = $response->json();
                $pkgData = $data['services']['donaciones']['paquete'] ?? [];

                ExtPaquete::updateOrCreate(
                    ['codigo_paquete' => $codigo],
                    [
                        'estado' => $pkgData['estado'] ?? 'desconocido',
                        'fecha_creacion' => $pkgData['fecha_creacion'] ?? null,
                        'datos_gateway' => $data, // Guardamos TODO el JSON aquí
                        'ultimo_sync' => now()
                    ]
                );
            }
        } catch (\Exception $e) {
            // Si falla el gateway, no rompemos el sync principal, solo lo logueamos
            Log::warning("Fallo sync gateway paquete {$codigo}: " . $e->getMessage());
        }
    }

    private function guardarItem($idDonExt, $idDetExt, $idProdExt, $campania, $don, $codigo, $prodLocal, $prodExt, $cant, $unidad, $donante, $alm, $est, $esp, $almData, $estado, $ubi, $codPaquete) 
    {
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
                'codigo_paquete' => $codPaquete, // Guardamos referencia
                'fecha_ultima_actualizacion' => now(),
            ]
        );
    }
}