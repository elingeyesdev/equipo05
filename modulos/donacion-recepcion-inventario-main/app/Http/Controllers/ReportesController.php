<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventario\Models\Donacione;
use Modules\Inventario\Models\Producto;
use Modules\Inventario\Models\Almacene;
use Modules\Inventario\Models\Campana;
use Modules\Inventario\Models\SolicitudesRecoleccion;
use Modules\Inventario\Models\RegistrosSalida;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('inventario::reportes.index');
    }

    // Reporte de donaciones por período
    public function donacionesPorPeriodo(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $donaciones = Donacione::whereBetween('fecha', [
            $request->fecha_inicio . ' 00:00:00',
            $request->fecha_fin . ' 23:59:59'
        ])
            ->with(['donante', 'campana', 'detalles', 'dinero'])
            ->get();

        $totalDonaciones = $donaciones->count();

        // Calcular monto total (solo donaciones en dinero)
        $totalMonto = $donaciones->filter(function ($donacion) {
            return $donacion->tipo === 'dinero' && $donacion->dinero;
        })->sum(function ($donacion) {
            return $donacion->dinero->monto ?? 0;
        });

        $donacionesPorTipo = $donaciones->groupBy('tipo')->map(function ($grupo) {
            $cantidadDinero = $grupo->filter(fn($d) => $d->tipo === 'dinero' && $d->dinero)->count();
            $montoDinero = $grupo->filter(fn($d) => $d->tipo === 'dinero' && $d->dinero)
                ->sum(fn($d) => $d->dinero->monto ?? 0);

            return [
                'cantidad' => $grupo->count(),
                'monto' => $montoDinero,
                'items' => $grupo->flatMap(fn($d) => $d->detalles)->count()
            ];
        });

        if ($request->formato === 'pdf') {
            return $this->exportarPDF('donaciones_periodo', compact(
                'donaciones',
                'totalDonaciones',
                'totalMonto',
                'donacionesPorTipo',
                'request'
            ));
        }

        if ($request->formato === 'excel') {
            return $this->exportarExcel('donaciones_periodo', $donaciones);
        }

        return view('inventario::reportes.donaciones_periodo', compact(
            'donaciones',
            'totalDonaciones',
            'totalMonto',
            'donacionesPorTipo',
            'request'
        ));
    }

    // Reporte de inventario por almacén
    public function inventarioPorAlmacen(Request $request)
    {
        $almacenId = $request->almacen_id;

        $almacenes = Almacene::with(['estantes.espacios.ubicacionesDonaciones.donacionDetalle.producto.categoriaProducto'])
            ->when($almacenId, function ($query) use ($almacenId) {
                return $query->where('id_almacen', $almacenId);
            })
            ->get();

        // Obtener productos agrupados por ubicación
        $ubicaciones = \Modules\Inventario\Models\UbicacionesDonacione::with([
            'donacionDetalle.producto.categoriaProducto',
            'espacio.estante.almacene'
        ])
            ->when($almacenId, function ($query) use ($almacenId) {
                return $query->whereHas('espacio.estante.almacene', function ($q) use ($almacenId) {
                    $q->where('id_almacen', $almacenId);
                });
            })
            ->whereHas('donacionDetalle.producto') // Solo ubicaciones con producto válido
            ->get();

        // Agrupar por producto para calcular totales
        $productosAgrupados = $ubicaciones->groupBy(function ($ubicacion) {
            return $ubicacion->donacionDetalle->id_producto ?? 0;
        })
            ->filter(function ($grupo, $key) {
                return $key > 0; // Filtrar productos con ID 0 o null
            })
            ->map(function ($grupo) {
                $primerItem = $grupo->first();
                $detalle = $primerItem->donacionDetalle;

                // Agrupar ubicaciones por almacén/estante/espacio y sumar cantidades
                $ubicacionesAgrupadas = $grupo->groupBy(function ($ub) {
                    $almacen = $ub->espacio->estante->almacene->nombre ?? 'N/A';
                    $estante = $ub->espacio->estante->codigo_estante ?? 'N/A';
                    $espacio = $ub->espacio->codigo_espacio ?? 'N/A';
                    return $almacen . '|' . $estante . '|' . $espacio;
                })->map(function ($ubicacionesGrupo) {
                    $primera = $ubicacionesGrupo->first();
                    return [
                        'almacen' => $primera->espacio->estante->almacene->nombre ?? 'N/A',
                        'estante' => $primera->espacio->estante->codigo_estante ?? 'N/A',
                        'espacio' => $primera->espacio->codigo_espacio ?? 'N/A',
                        'cantidad' => $ubicacionesGrupo->sum('cantidad_ubicada')
                    ];
                })->values();

                return (object) [
                    'id_producto' => $detalle->id_producto ?? 0,
                    'nombre_producto' => $detalle->producto->nombre ?? 'N/A',
                    'categoria' => $detalle->producto->categoriaProducto->nombre ?? 'N/A',
                    'cantidad_total' => $grupo->sum('cantidad_ubicada'),
                    'ubicaciones' => $ubicacionesAgrupadas
                ];
            })->values();

        $totalProductos = $productosAgrupados->count();
        $cantidadTotal = $productosAgrupados->sum('cantidad_total');

        $productosCategoria = $productosAgrupados->groupBy('categoria')->map(function ($grupo) {
            return [
                'cantidad' => $grupo->sum('cantidad_total'),
                'items' => $grupo->count()
            ];
        });

        if ($request->formato === 'pdf') {
            return $this->exportarPDF('inventario_almacen', compact(
                'almacenes',
                'productosAgrupados',
                'totalProductos',
                'cantidadTotal',
                'productosCategoria',
                'almacenId'
            ));
        }

        if ($request->formato === 'excel') {
            return $this->exportarExcel('inventario_almacen', $productosAgrupados);
        }

        return view('inventario::reportes.inventario_almacen', compact(
            'almacenes',
            'productosAgrupados',
            'totalProductos',
            'cantidadTotal',
            'productosCategoria',
            'almacenId'
        ));
    }

    // Reporte de solicitudes de recolección
    public function solicitudesRecoleccion(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'nullable|string',
        ]);

        $solicitudes = SolicitudesRecoleccion::with(['donante', 'usuario'])
            ->when($request->fecha_inicio && $request->fecha_fin, function ($query) use ($request) {
                return $query->whereBetween('fecha_programada', [
                    $request->fecha_inicio,
                    $request->fecha_fin
                ]);
            })
            ->when($request->estado, function ($query) use ($request) {
                return $query->where('estado', $request->estado);
            })
            ->get();

        $totalSolicitudes = $solicitudes->count();
        $solicitudesPorEstado = $solicitudes->groupBy('estado')->map->count();

        if ($request->formato === 'pdf') {
            return $this->exportarPDF('solicitudes_recoleccion', compact(
                'solicitudes',
                'totalSolicitudes',
                'solicitudesPorEstado',
                'request'
            ));
        }

        if ($request->formato === 'excel') {
            return $this->exportarExcel('solicitudes_recoleccion', $solicitudes);
        }

        return view('inventario::reportes.solicitudes_recoleccion', compact(
            'solicitudes',
            'totalSolicitudes',
            'solicitudesPorEstado',
            'request'
        ));
    }

    // Reporte de salidas de productos
    public function salidasProductos(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        $salidas = RegistrosSalida::with([
            'paquete.detalles.donacionDetalle.producto'
        ])
            ->when($request->fecha_inicio && $request->fecha_fin, function ($query) use ($request) {
                return $query->whereBetween('fecha_salida', [
                    $request->fecha_inicio . ' 00:00:00',
                    $request->fecha_fin . ' 23:59:59'
                ]);
            })
            ->get();

        $totalSalidas = $salidas->count();
        $cantidadTotal = $salidas->sum(function ($salida) {
            return $salida->paquete->detalles->sum('cantidad_usada');
        });

        // Agrupar productos por salida
        $salidasDetalladas = $salidas->map(function ($salida) {
            return [
                'id_salida' => $salida->id_salida,
                'fecha_salida' => $salida->fecha_salida,
                'destino' => $salida->destino,
                'paquete_codigo' => $salida->paquete->codigo_paquete ?? 'N/A',
                'productos' => $salida->paquete->detalles->map(function ($detalle) {
                    return [
                        'nombre' => $detalle->donacionDetalle->producto->nombre ?? 'N/A',
                        'cantidad' => $detalle->cantidad_usada
                    ];
                })
            ];
        });

        if ($request->formato === 'pdf') {
            return $this->exportarPDF('salidas_productos', compact(
                'salidasDetalladas',
                'totalSalidas',
                'cantidadTotal',
                'request'
            ));
        }

        if ($request->formato === 'excel') {
            return $this->exportarExcel('salidas_productos', $salidasDetalladas);
        }

        return view('inventario::reportes.salidas_productos', compact(
            'salidasDetalladas',
            'totalSalidas',
            'cantidadTotal',
            'request'
        ));
    }

    // Reporte de campañas
    public function campanasReporte(Request $request)
    {
        $campanas = Campana::with(['donaciones.dinero', 'donaciones.detalles'])
            ->when($request->estado, function ($query) use ($request) {
                $now = Carbon::now();
                if ($request->estado === 'activas') {
                    return $query->where('fecha_inicio', '<=', $now)
                        ->where('fecha_fin', '>=', $now);
                } elseif ($request->estado === 'finalizadas') {
                    return $query->where('fecha_fin', '<', $now);
                } elseif ($request->estado === 'proximas') {
                    return $query->where('fecha_inicio', '>', $now);
                }
            })
            ->get();

        $totalCampanas = $campanas->count();
        $montoTotalRecaudado = $campanas->sum(function ($campana) {
            return $campana->donaciones
                ->filter(fn($d) => $d->tipo === 'dinero' && $d->dinero)
                ->sum(fn($d) => $d->dinero->monto ?? 0);
        });

        if ($request->formato === 'pdf') {
            return $this->exportarPDF('campanas_reporte', compact(
                'campanas',
                'totalCampanas',
                'montoTotalRecaudado',
                'request'
            ));
        }

        if ($request->formato === 'excel') {
            return $this->exportarExcel('campanas_reporte', $campanas);
        }

        return view('inventario::reportes.campanas_reporte', compact(
            'campanas',
            'totalCampanas',
            'montoTotalRecaudado',
            'request'
        ));
    }

    // Reporte de distribución de paquetes
    public function reporteDistribucion(Request $request)
    {
        // Get all distribution records with their packages
        $salidas = RegistrosSalida::with([
            'paquete.detalles.donacionDetalle.producto'
        ])
            ->when($request->fecha_inicio && $request->fecha_fin, function ($query) use ($request) {
                return $query->whereBetween('fecha_salida', [
                    $request->fecha_inicio . ' 00:00:00',
                    $request->fecha_fin . ' 23:59:59'
                ]);
            })
            ->when($request->destino, function ($query) use ($request) {
                return $query->where('destino', 'like', '%' . $request->destino . '%');
            })
            ->when($request->encargado, function ($query) use ($request) {
                return $query->where('encargado', 'like', '%' . $request->encargado . '%');
            })
            ->orderBy('fecha_salida', 'desc')
            ->get();

        // Get all packages
        $totalPaquetes = \Modules\Inventario\Models\Paquete::count();

        // KPIs
        $totalDistribuido = $salidas->count();
        $pendienteDistribucion = $totalPaquetes - $totalDistribuido;

        // Top destinations
        $destinosFrecuentes = $salidas->groupBy('destino')
            ->map->count()
            ->sortDesc()
            ->take(10);

        $destinoMasFrecuente = $destinosFrecuentes->keys()->first() ?? 'N/A';
        $destinoMasFrecuenteCount = $destinosFrecuentes->first() ?? 0;

        // Last shipment
        $ultimoEnvio = $salidas->first();
        $ultimoEnvioFecha = $ultimoEnvio ? \Carbon\Carbon::parse($ultimoEnvio->fecha_salida)->format('d/m/Y') : 'N/A';

        // Distribution by month (last 12 months)
        $distribucionMensual = $salidas
            ->groupBy(function ($salida) {
                return \Carbon\Carbon::parse($salida->fecha_salida)->format('Y-m');
            })
            ->map->count()
            ->sortKeys()
            ->take(12);

        // Prepare detailed data
        $salidasDetalladas = $salidas->map(function ($salida) {
            $productos = $salida->paquete ? $salida->paquete->detalles->map(function ($detalle) {
                return [
                    'nombre' => $detalle->donacionDetalle->producto->nombre ?? 'N/A',
                    'cantidad' => $detalle->cantidad_usada
                ];
            })->values() : collect();

            return [
                'id_salida' => $salida->id_salida,
                'codigo_paquete' => $salida->paquete->codigo_paquete ?? 'N/A',
                'fecha_salida' => $salida->fecha_salida,
                'destino' => $salida->destino,
                'encargado' => $salida->encargado,
                'observaciones' => $salida->observaciones,
                'productos' => $productos,
                'total_items' => $productos->sum('cantidad')
            ];
        });

        // Get unique destinations and responsibles for filters
        $destinosUnicos = RegistrosSalida::select('destino')
            ->distinct()
            ->whereNotNull('destino')
            ->orderBy('destino')
            ->pluck('destino');

        $encargadosUnicos = RegistrosSalida::select('encargado')
            ->distinct()
            ->whereNotNull('encargado')
            ->orderBy('encargado')
            ->pluck('encargado');

        if ($request->formato === 'pdf') {
            return $this->exportarPDF('distribucion_paquetes', compact(
                'salidasDetalladas',
                'totalDistribuido',
                'pendienteDistribucion',
                'destinoMasFrecuente',
                'request'
            ));
        }

        if ($request->formato === 'excel') {
            return $this->exportarExcel('distribucion_paquetes', $salidasDetalladas);
        }

        return view('inventario::reportes.distribucion', compact(
            'totalDistribuido',
            'pendienteDistribucion',
            'destinoMasFrecuente',
            'destinoMasFrecuenteCount',
            'ultimoEnvioFecha',
            'destinosFrecuentes',
            'distribucionMensual',
            'salidasDetalladas',
            'destinosUnicos',
            'encargadosUnicos',
            'request'
        ));
    }

    private function exportarPDF($vista, $data)
    {
        $pdf = app('dompdf.wrapper');
        $pdf->loadView("reportes.pdf.{$vista}", $data);
        return $pdf->download("reporte_{$vista}_" . date('Y-m-d') . '.pdf');
    }

    private function exportarExcel($nombre, $datos)
    {
        $filename = "reporte_{$nombre}_" . date('Y-m-d_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($datos, $nombre) {
            echo "\xEF\xBB\xBF"; // UTF-8 BOM

            // Estilos HTML/CSS para Excel
            echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
            echo '<style>';
            echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
            echo 'th { background-color: #1B263B; color: white; font-weight: bold; padding: 12px; border: 1px solid #ddd; text-align: left; }';
            echo 'td { padding: 10px; border: 1px solid #ddd; }';
            echo 'tr:nth-child(even) { background-color: #f8f9fa; }';
            echo '.header { background-color: #FFB700; color: #1B263B; font-size: 18px; font-weight: bold; padding: 15px; text-align: center; }';
            echo '.info { background-color: #E0E1DD; padding: 10px; margin: 10px 0; }';
            echo '.amount { color: #28a745; font-weight: bold; }';
            echo '.badge { padding: 4px 8px; border-radius: 3px; font-size: 11px; font-weight: bold; }';
            echo '</style>';
            echo '</head>';
            echo '<body>';

            if ($nombre === 'donaciones_periodo') {
                echo '<div class="header">📊 REPORTE DE DONACIONES</div>';
                echo '<div class="info">Generado: ' . now()->format('d/m/Y H:i') . '</div>';
                echo '<table>';
                echo '<thead><tr>';
                echo '<th>ID</th><th>Fecha</th><th>Donante</th><th>Campaña</th><th>Tipo</th><th>Monto</th><th>Items</th>';
                echo '</tr></thead><tbody>';

                foreach ($datos as $item) {
                    $monto = 0;
                    $items = 0;
                    if ($item->tipo === 'dinero' && $item->dinero) {
                        $monto = $item->dinero->monto;
                    }
                    $items = $item->detalles->count();

                    echo '<tr>';
                    echo '<td>' . $item->id_donacion . '</td>';
                    echo '<td>' . \Carbon\Carbon::parse($item->fecha)->format('d/m/Y H:i') . '</td>';
                    echo '<td>' . ($item->donante->nombre ?? 'Anónimo') . '</td>';
                    echo '<td>' . ($item->campana->nombre ?? 'General') . '</td>';
                    echo '<td><span class="badge">' . ucfirst($item->tipo) . '</span></td>';
                    echo '<td class="amount">Bs. ' . number_format($monto, 2) . '</td>';
                    echo '<td>' . $items . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';

            } elseif ($nombre === 'inventario_almacen') {
                echo '<div class="header">📦 REPORTE DE INVENTARIO</div>';
                echo '<div class="info">Generado: ' . now()->format('d/m/Y H:i') . '</div>';
                echo '<table>';
                echo '<thead><tr>';
                echo '<th>ID</th><th>Producto</th><th>Categoría</th><th>Cantidad Total</th><th>Ubicaciones</th>';
                echo '</tr></thead><tbody>';

                foreach ($datos as $item) {
                    $ubicacionesStr = $item->ubicaciones->map(function ($ub) {
                        return $ub['almacen'] . ' / ' . $ub['estante'] . ' / ' . $ub['espacio'] . ' (' . $ub['cantidad'] . ')';
                    })->join('; ');

                    echo '<tr>';
                    echo '<td>' . $item->id_producto . '</td>';
                    echo '<td><strong>' . $item->nombre_producto . '</strong></td>';
                    echo '<td>' . $item->categoria . '</td>';
                    echo '<td style="text-align: center; font-weight: bold;">' . $item->cantidad_total . '</td>';
                    echo '<td>' . $ubicacionesStr . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';

            } elseif ($nombre === 'solicitudes_recoleccion') {
                echo '<div class="header">🚚 REPORTE DE SOLICITUDES DE RECOLECCIÓN</div>';
                echo '<div class="info">Generado: ' . now()->format('d/m/Y H:i') . '</div>';
                echo '<table>';
                echo '<thead><tr>';
                echo '<th>ID</th><th>Donante</th><th>Dirección</th><th>Fecha Programada</th><th>Estado</th><th>Recolector</th>';
                echo '</tr></thead><tbody>';

                foreach ($datos as $item) {
                    $recolectorNombre = 'Sin asignar';
                    if ($item->usuario) {
                        $recolectorNombre = trim($item->usuario->nombres . ' ' . $item->usuario->apellidos);
                    }

                    echo '<tr>';
                    echo '<td>' . $item->id_solicitud . '</td>';
                    echo '<td>' . ($item->donante->nombre ?? 'N/A') . '</td>';
                    echo '<td>' . $item->direccion_recoleccion . '</td>';
                    echo '<td>' . \Carbon\Carbon::parse($item->fecha_programada)->format('d/m/Y H:i') . '</td>';
                    echo '<td><span class="badge">' . $item->estado . '</span></td>';
                    echo '<td>' . $recolectorNombre . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';

            } elseif ($nombre === 'salidas_productos') {
                echo '<div class="header">📤 REPORTE DE SALIDAS DE PRODUCTOS</div>';
                echo '<div class="info">Generado: ' . now()->format('d/m/Y H:i') . '</div>';
                echo '<table>';
                echo '<thead><tr>';
                echo '<th>ID</th><th>Fecha Salida</th><th>Destino</th><th>Paquete</th><th>Productos</th>';
                echo '</tr></thead><tbody>';

                foreach ($datos as $salida) {
                    $productosStr = collect($salida['productos'])->map(function ($p) {
                        return $p['nombre'] . ' (' . $p['cantidad'] . ')';
                    })->join('; ');

                    echo '<tr>';
                    echo '<td>' . $salida['id_salida'] . '</td>';
                    echo '<td>' . \Carbon\Carbon::parse($salida['fecha_salida'])->format('d/m/Y H:i') . '</td>';
                    echo '<td>' . ($salida['destino'] ?? '-') . '</td>';
                    echo '<td>' . $salida['paquete_codigo'] . '</td>';
                    echo '<td>' . $productosStr . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';

            } elseif ($nombre === 'campanas_reporte') {
                echo '<div class="header">📢 REPORTE DE CAMPAÑAS</div>';
                echo '<div class="info">Generado: ' . now()->format('d/m/Y H:i') . '</div>';
                echo '<table>';
                echo '<thead><tr>';
                echo '<th>ID</th><th>Nombre</th><th>Fecha Inicio</th><th>Fecha Fin</th><th>Total Donaciones</th><th>Monto Recaudado</th>';
                echo '</tr></thead><tbody>';

                foreach ($datos as $item) {
                    $montoDinero = $item->donaciones
                        ->filter(fn($d) => $d->tipo === 'dinero' && $d->dinero)
                        ->sum(fn($d) => $d->dinero->monto ?? 0);

                    echo '<tr>';
                    echo '<td>' . $item->id_campana . '</td>';
                    echo '<td><strong>' . $item->nombre . '</strong></td>';
                    echo '<td>' . \Carbon\Carbon::parse($item->fecha_inicio)->format('d/m/Y') . '</td>';
                    echo '<td>' . \Carbon\Carbon::parse($item->fecha_fin)->format('d/m/Y') . '</td>';
                    echo '<td style="text-align: center;">' . $item->donaciones->count() . '</td>';
                    echo '<td class="amount">Bs. ' . number_format($montoDinero, 2) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } elseif ($nombre === 'distribucion_paquetes') {
                echo '<div class="header">📦 REPORTE DE DISTRIBUCIÓN DE PAQUETES</div>';
                echo '<div class="info">Generado: ' . now()->format('d/m/Y H:i') . '</div>';
                echo '<table>';
                echo '<thead><tr>';
                echo '<th>ID Salida</th>';
                echo '<th>Fecha Salida</th>';
                echo '<th>Destino</th>';
                echo '<th>Encargado</th>';
                echo '<th>Paquete Código</th>';
                echo '<th>Productos</th>';
                echo '</tr></thead><tbody>';

                foreach ($datos as $salida) {
                    $productosStr = collect($salida['productos'])->map(function ($p) {
                        return $p['nombre'] . ' (' . $p['cantidad'] . ')';
                    })->join('; ');

                    echo '<tr>';
                    echo '<td>' . $salida['id_salida'] . '</td>';
                    echo '<td>' . \Carbon\Carbon::parse($salida['fecha_salida'])->format('d/m/Y H:i') . '</td>';
                    echo '<td>' . ($salida['destino'] ?? '-') . '</td>';
                    echo '<td>' . ($salida['encargado'] ?? '-') . '</td>';
                    echo '<td>' . ($salida['paquete_codigo'] ?? '-') . '</td>';
                    echo '<td>' . $productosStr . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }

            echo '</body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }
}







