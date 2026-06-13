<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\Paquete;
use Modules\Inventario\Models\SolicitudesRecoleccion;
use Modules\Inventario\Models\Donacione;
use Modules\Inventario\Models\DonacionDetalle;
use Modules\Inventario\Models\PaqueteDetalle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\PaqueteRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class PaqueteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->assertAnyPermission('inventario.paquetes.gestionar', 'inventario.paquetes.ver');
        $paquetes = Paquete::orderByDesc('fecha_creacion')->get();

        return view('inventario::paquete.index', compact('paquetes'))
            ->with('i', 0);
    }

    /**
     * Display pending package requests
     */
    public function pendientes(): View
    {
        return view('inventario::paquete.pendientes');
    }

    /**
     * Helper para obtener productos con stock agrupado
     */
    private function getProductosConStock()
    {
        // Obtener todos los detalles de donaciones en especie
        $detalles = DonacionDetalle::with(['producto', 'paqueteDetalles'])
            ->whereHas('donacion', function ($query) {
                $query->where('tipo', 'especie');
            })
            ->get();

        $productosAgrupados = [];

        foreach ($detalles as $detalle) {
            $idProducto = $detalle->id_producto;

            // Calcular cantidad ya usada en otros paquetes
            $usado = $detalle->paqueteDetalles->sum('cantidad_usada');
            $disponible = $detalle->cantidad - $usado;

            if ($disponible > 0) {
                if (!isset($productosAgrupados[$idProducto])) {
                    $productosAgrupados[$idProducto] = [
                        'id_producto' => $idProducto,
                        'nombre' => $detalle->producto->nombre ?? 'Producto Desconocido',
                        'descripcion' => $detalle->producto->descripcion ?? '', // Usar descripción del producto base
                        'unidad_medida' => $detalle->unidad_medida,
                        'total_disponible' => 0
                    ];
                }
                $productosAgrupados[$idProducto]['total_disponible'] += $disponible;
            }
        }

        return array_values($productosAgrupados);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $paquete = new Paquete();
        $solicitudes = SolicitudesRecoleccion::all();
        $productosDisponibles = $this->getProductosConStock();

        return view('inventario::paquete.create', compact('paquete', 'solicitudes', 'productosDisponibles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(PaqueteRequest $request): RedirectResponse
    {
        $data = $request->validated();

        \Log::info('=== INICIO STORE PAQUETE ===');
        \Log::info('Datos validados:', $data);

        if (empty($data['codigo_paquete'])) {
            $data['codigo_paquete'] = $this->generarCodigoPaquete();
        }
        $data['fecha_creacion'] = now();
        $data['ci_usuario_registro'] = auth()->user()->ci ?? null;

        try {
            $paquete = null;
            $primeraUbicacion = null;

            DB::transaction(function () use ($data, $request, &$paquete, &$primeraUbicacion) {
                \Log::info('Iniciando transacción...');

                $paquete = Paquete::create($data);
                \Log::info('Paquete creado:', ['id' => $paquete->id_paquete, 'codigo' => $paquete->codigo_paquete]);

                if ($request->has('detalles')) {
                    \Log::info('Procesando detalles del paquete...', ['cantidad_detalles' => count($request->detalles)]);
                    $primeraUbicacion = $this->procesarDetallesPaquete($paquete, $request->detalles);
                }

                \Log::info('Transacción completada exitosamente');
            });

            // Si hay paquete externo, enviar PATCH al sistema ADS
            if ($request->has('paquete_externo_id') && !empty($request->paquete_externo_id)) {
                $this->notificarSistemaExterno($request->paquete_externo_id, $primeraUbicacion);
            }

            \Log::info('=== FIN STORE PAQUETE (SUCCESS) ===');
            return Redirect::route('inventario.paquete.index')
                ->with('success', 'Paquete creado exitosamente.');

        } catch (\Exception $e) {
            \Log::error('=== ERROR EN STORE PAQUETE ===');
            \Log::error('Mensaje: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());

            return Redirect::back()
                ->withInput()
                ->withErrors(['error' => 'Error al guardar el paquete: ' . $e->getMessage()]);
        }
    }

    /**
     * Lógica FIFO para asignar productos desde los lotes de donación
     */
    private function procesarDetallesPaquete($paquete, $detallesSolicitados)
    {
        $primeraUbicacion = null;

        foreach ($detallesSolicitados as $index => $solicitud) {
            if (empty($solicitud['id_producto']) || empty($solicitud['cantidad_usada'])) {
                continue;
            }

            $cantidadRequerida = $solicitud['cantidad_usada'];
            $idProducto = $solicitud['id_producto'];

            \Log::info("Procesando detalle #{$index}: Producto ID {$idProducto}, Cantidad {$cantidadRequerida}");

            // Buscar lotes disponibles ordenados por fecha (FIFO)
            $lotes = DonacionDetalle::where('id_producto', $idProducto)
                ->whereHas('donacion', function ($q) {
                    $q->where('tipo', 'especie');
                })
                ->with(['donacion', 'paqueteDetalles', 'ubicaciones.espacio.estante.almacen'])
                ->get()
                ->sortBy(function ($lote) {
                    return $lote->donacion->fecha;
                });

            foreach ($lotes as $lote) {
                if ($cantidadRequerida <= 0)
                    break;

                $usado = $lote->paqueteDetalles->sum('cantidad_usada');
                $disponibleEnLote = $lote->cantidad - $usado;

                if ($disponibleEnLote > 0) {
                    $cantidadATomar = min($cantidadRequerida, $disponibleEnLote);

                    $detalle = PaqueteDetalle::create([
                        'id_paquete' => $paquete->id_paquete,
                        'id_detalle_donacion' => $lote->id_detalle,
                        'cantidad_usada' => $cantidadATomar
                    ]);

                    \Log::info("Asignado del lote {$lote->id_detalle}: {$cantidadATomar} unidades. Detalle ID: {$detalle->id_paquete_detalle}");

                    // Obtener la primera ubicación si aún no la tenemos
                    if (!$primeraUbicacion && $lote->ubicaciones->isNotEmpty()) {
                        $ubicacion = $lote->ubicaciones->first();
                        if ($ubicacion && $ubicacion->espacio && $ubicacion->espacio->estante && $ubicacion->espacio->estante->almacen) {
                            $almacen = $ubicacion->espacio->estante->almacen;
                            $primeraUbicacion = [
                                'direccion' => $almacen->direccion ?? 'Dirección no especificada',
                                'latitud' => $almacen->latitud ?? '0',
                                'longitud' => $almacen->longitud ?? '0'
                            ];
                        }
                    }

                    $cantidadRequerida -= $cantidadATomar;
                }
            }

            if ($cantidadRequerida > 0) {
                throw new \Exception("No hay suficiente stock disponible para el producto ID: $idProducto. Faltan $cantidadRequerida unidades.");
            }
        }

        return $primeraUbicacion;
    }

    /**
     * Notificar al sistema externo (ADS) que el paquete está armado
     */
    private function notificarSistemaExterno($idPaqueteExterno, $primeraUbicacion)
    {
        try {
            $url = route('gateway.logistica.paquetes.armar', ['id' => $idPaqueteExterno]);

            // Obtener CI del usuario logueado
            $ciUsuario = auth()->user()->ci ?? 'Sin CI';

            // Formatear ubicación
            $ubicacionActual = 'Ubicación no especificada';
            if ($primeraUbicacion) {
                $ubicacionActual = sprintf(
                    "%s-(%s, %s)",
                    $primeraUbicacion['direccion'],
                    $primeraUbicacion['latitud'],
                    $primeraUbicacion['longitud']
                );
            }

            $body = [
                'ci_usuario' => $ciUsuario,
                'ubicacion_actual' => $ubicacionActual
            ];  

            \Log::info('Enviando PATCH al sistema ADS', [
                'url' => $url,
                'body' => $body
            ]);

            // Enviar petición PATCH
            $client = new \GuzzleHttp\Client();
            $response = $client->patch($url, [
                'json' => $body,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ]
            ]);

            \Log::info('Respuesta del sistema ADS', [
                'status' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents()
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al notificar al sistema externo ADS', [
                'mensaje' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // No lanzamos excepción para no bloquear el guardado del paquete local
        }
    }

    /**
     * Generar código único para el paquete
     */
    private function generarCodigoPaquete(): string
    {
        do {
            // Formato: PKG-YYYYMMDD-XXXX (ej: PKG-20231126-0001)
            $codigo = 'PKG-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Paquete::where('codigo_paquete', $codigo)->exists());

        return $codigo;
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $paquete = Paquete::with(['paqueteDetalles.donacionDetalle.producto', 'paqueteDetalles.donacionDetalle.donacion.donante'])->find($id);

        return view('inventario::paquete.show', compact('paquete'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $paquete = Paquete::with(['paqueteDetalles.donacionDetalle.producto'])->find($id);
        $solicitudes = SolicitudesRecoleccion::all();
        $productosDisponibles = $this->getProductosConStock();

        // Necesitamos transformar los detalles actuales del paquete para que coincidan con la estructura agrupada
        // Agrupamos los detalles del paquete por producto para mostrar la cantidad total usada de ese producto en este paquete
        $detallesAgrupados = [];
        foreach ($paquete->paqueteDetalles as $detalle) {
            $prodId = $detalle->donacionDetalle->id_producto;
            if (!isset($detallesAgrupados[$prodId])) {
                $detallesAgrupados[$prodId] = [
                    'id_producto' => $prodId,
                    'cantidad_usada' => 0
                ];
            }
            $detallesAgrupados[$prodId]['cantidad_usada'] += $detalle->cantidad_usada;
        }

        // Inyectamos estos detalles agrupados en el objeto paquete para que la vista los use
        $paquete->detalles_agrupados = array_values($detallesAgrupados);

        return view('inventario::paquete.edit', compact('paquete', 'solicitudes', 'productosDisponibles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaqueteRequest $request, Paquete $paquete): RedirectResponse
    {
        $data = $request->validated();

        \Log::info('=== INICIO UPDATE PAQUETE ===');
        \Log::info('ID Paquete:', ['id' => $paquete->id_paquete]);

        try {
            DB::transaction(function () use ($data, $request, $paquete) {
                \Log::info('Iniciando transacción de actualización...');

                $paquete->update($data);

                // Liberar stock anterior (borrar detalles previos)
                \Log::info('Eliminando detalles anteriores...');
                $paquete->paqueteDetalles()->delete();

                if ($request->has('detalles')) {
                    \Log::info('Procesando nuevos detalles...');
                    $this->procesarDetallesPaquete($paquete, $request->detalles);
                }

                \Log::info('Transacción de actualización completada');
            });

            \Log::info('=== FIN UPDATE PAQUETE (SUCCESS) ===');
            return Redirect::route('inventario.paquete.index')
                ->with('success', 'Paquete actualizado exitosamente');

        } catch (\Exception $e) {
            \Log::error('=== ERROR EN UPDATE PAQUETE ===');
            \Log::error('Mensaje: ' . $e->getMessage());

            return Redirect::back()
                ->withInput()
                ->withErrors(['error' => 'Error al actualizar el paquete: ' . $e->getMessage()]);
        }
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        // Validate deletion reason
        $request->validate([
            'deleted_reason' => 'required|string|min:10|max:500'
        ], [
            'deleted_reason.required' => 'El motivo de eliminación es obligatorio.',
            'deleted_reason.min' => 'El motivo debe tener al menos 10 caracteres.',
            'deleted_reason.max' => 'El motivo no puede exceder 500 caracteres.'
        ]);

        $paquete = Paquete::find($id);

        if ($paquete) {
            try {
                DB::transaction(function () use ($paquete, $request) {
                    // Store deletion metadata before deleting
                    $paquete->deleted_reason = $request->deleted_reason;
                    $paquete->deleted_by = auth()->user()->ci ?? null;
                    $paquete->save();

                    // Primero eliminamos los detalles para mantener integridad si no hay cascade
                    $paquete->paqueteDetalles()->delete();
                    $paquete->delete();
                });
                return Redirect::route('inventario.paquete.index')->with('success', 'Paquete eliminado exitosamente');
            } catch (\Exception $e) {
                return Redirect::back()->withErrors(['error' => 'Error al eliminar el paquete: ' . $e->getMessage()]);
            }
        }

        return Redirect::route('inventario.paquete.index')->withErrors(['error' => 'Paquete no encontrado']);
    }
}







