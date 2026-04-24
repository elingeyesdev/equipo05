<?php

namespace App\Http\Controllers;

use App\Models\TrazabilidadItem;
use App\Models\Campania;
use App\Models\Ext\ExtAlmacen;
use App\Models\Ext\ExtPaquete; // <--- Modelo local de paquetes
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TrazabilidadExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReporteTrazabilidadController extends Controller
{
    /**
     * Muestra el inventario global agrupado por almacenes.
     */
    public function index(Request $request)
    {
        // 1. Recuperar Campañas para el filtro
        $campanias = Campania::orderBy('fechainicio', 'desc')->get();
        
        // 2. Filtros recibidos
        $campaniaId = $request->input('campaniaid');
        $buscar     = $request->input('buscar');

        // 3. Traemos los almacenes
        $almacenes = ExtAlmacen::orderBy('nombre')->get();

        // 4. Llenamos cada almacén aplicando los filtros
        foreach ($almacenes as $alm) {
            
            // Query base: Items que están FÍSICAMENTE en este almacén
            $queryBase = TrazabilidadItem::where('almacenid', $alm->almacenid)
                                         ->where('estado_actual', 'En almacén');

            // Aplicamos filtros si el usuario los seleccionó
            if ($campaniaId) {
                $queryBase->where('campaniaid', $campaniaId);
            }
            if ($buscar) {
                $queryBase->where(function($q) use ($buscar) {
                    $q->where('codigo_unico', 'ilike', "%$buscar%")
                      ->orWhere('nombre_producto', 'ilike', "%$buscar%");
                });
            }

            // A) Productos (Lista detallada)
            $alm->lista_productos = (clone $queryBase)
                                    ->orderBy('estante_codigo')
                                    ->orderBy('espacio_codigo')
                                    ->get();

            // B) Paquetes (Agrupados)
            $alm->lista_paquetes = (clone $queryBase)
                                    ->select('codigo_paquete', 'fecha_creacion_paquete', DB::raw('count(*) as items_count'))
                                    ->whereNotNull('codigo_paquete')
                                    ->groupBy('codigo_paquete', 'fecha_creacion_paquete')
                                    ->get();
        }

        return view('reportes.trazabilidad.index', compact('almacenes', 'campanias'));
    }

    /**
     * AJAX Optimizado: Intenta leer de DB local primero, si no existe, va a la API.
     */
    public function verPaqueteAjax($codigo)
    {
        // 1) Buscar en local SOLO si fue sincronizado hace 1 minuto o menos
        $paqueteLocal = ExtPaquete::where('codigo_paquete', $codigo)
            ->where('ultimo_sync', '>=', now()->subMinute())
            ->first();

        if ($paqueteLocal && !empty($paqueteLocal->datos_gateway)) {
            return response()->json([
                'success' => true,
                'data'    => $paqueteLocal->datos_gateway,
                'source'  => 'local',
            ]);
        }

        // 2) Si no existe o está desactualizado, consultar Gateway
        $gatewayUrl = rtrim(config('services.externos.gateway_url'), '/');

        try {
            $response = Http::timeout(8)
                ->get("{$gatewayUrl}/api/gateway/trazabilidad/paquete/{$codigo}");

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Información no disponible en Gateway.',
                ], 500);
            }

            $data = $response->json();

            // 3) Guardar / actualizar en tabla local
            ExtPaquete::updateOrCreate(
                ['codigo_paquete' => $codigo],
                [
                    'estado'         => data_get($data, 'services.donaciones.paquete.estado'),
                    'fecha_creacion' => data_get($data, 'services.donaciones.paquete.fecha_creacion'),
                    'datos_gateway'  => $data, // JSON completo
                    'ultimo_sync'    => now(),
                ]
            );

            return response()->json([
                'success' => true,
                'data'    => $data,
                'source'  => 'api',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión con Gateway.',
            ], 500);
        }
    }


    /**
     * Vista completa del detalle del paquete (usada si se accede por URL directa)
     */
    public function verPaquete($codigo)
    {
        // Misma lógica: Local primero, luego API
        $paqueteLocal = ExtPaquete::where('codigo_paquete', $codigo)->first();

        if ($paqueteLocal && !empty($paqueteLocal->datos_gateway)) {
            $data = $paqueteLocal->datos_gateway;
        } else {
            $gatewayUrl = config('services.externos.gateway_url');
            $response = Http::get("{$gatewayUrl}/api/gateway/trazabilidad/paquete/{$codigo}");

            if ($response->failed()) {
                return back()->withErrors('No se pudo conectar con el Gateway.');
            }
            $data = $response->json();
        }

        return view('reportes.trazabilidad.detalle_paquete', compact('data', 'codigo'));
    }
    
    /**
     * Exportación a PDF (Optimizada con JOIN local)
     */
    public function exportarPDF(Request $request)
    {
        $query = TrazabilidadItem::query();

        if ($request->filled('campaniaid')) {
            $query->where('campaniaid', $request->campaniaid);
        }

        // 👇 Trae datos del gateway desde la tabla local
        $items = $query->leftJoin('ext_paquetes', 'trazabilidad_items.codigo_paquete', '=', 'ext_paquetes.codigo_paquete')
            ->select(
                'trazabilidad_items.*',
                'ext_paquetes.datos_gateway as datos_gateway'
            )
            ->orderBy('almacen_nombre', 'asc')
            ->orderBy('estante_codigo', 'asc')
            ->orderBy('espacio_codigo', 'asc')
            ->orderBy('fecha_donacion', 'desc')
            ->get();

        $campaniaNombre = $request->filled('campaniaid')
            ? Campania::find($request->campaniaid)?->titulo
            : 'Reporte General de Todas las Campañas';

        $fechaGeneracion = now()->format('d/m/Y h:i A');

        $pdf = Pdf::loadView('reportes.trazabilidad.pdf', compact(
            'items',
            'campaniaNombre',
            'fechaGeneracion'
        ))->setPaper('letter', 'landscape');

        return $pdf->download('trazabilidad_' . date('Y-m-d_His') . '.pdf');
    }


    /**
     * Exportación a Excel
     */
    public function exportarExcel(Request $request)
    {
        return Excel::download(
            new TrazabilidadExport($request->all()), 
            'trazabilidad_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}