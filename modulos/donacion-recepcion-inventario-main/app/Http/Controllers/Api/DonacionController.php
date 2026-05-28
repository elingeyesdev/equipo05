<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\Donacione;
use Modules\Inventario\Models\DonacionesDinero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DonacionController extends Controller
{
    /**
     * POST /api/donaciones
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_donacion' => 'required|in:dinero,especie,ropa',
            'fecha_donacion' => 'required|date',
            'id_donante' => 'required|exists:inventario.donantes,id_donante',
            'id_campana' => 'nullable|exists:inventario.campanas,id_campana',
        ]);

        try {
            $donacion = Donacione::create([
                'tipo' => $validated['tipo_donacion'],
                'fecha' => $validated['fecha_donacion'],
                'id_donante' => $validated['id_donante'],
                'id_campana' => $validated['id_campana'] ?? null,
            ]);

            return response()->json([
                'id' => $donacion->id_donacion,
                'message' => 'Donación creada exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creando donación: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al crear donación',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/donaciones-en-dinero/{id}
     */
    public function updateMoneyDonation($id, Request $request)
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'divisa' => 'nullable|string|max:10',
            'nombre_cuenta' => 'nullable|string|max:255',
            'numero_cuenta' => 'nullable|string|max:255',
            'comprobante_url' => 'nullable|string',
            'estado_validacion' => 'nullable|string|in:pendiente,validado,rechazado',
        ]);

        try {
            $comprobantePath = null;
            if ($request->has('comprobante_url') && !empty($request->comprobante_url)) {
                $base64Image = $request->comprobante_url;
                
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    $type = strtolower($type[1]);
                } else {
                    $type = 'jpg';
                }
                
                $imageData = base64_decode($base64Image);
                $filename = 'comprobante_' . time() . '.' . $type;
                Storage::disk('public')->put('comprobantes/' . $filename, $imageData);
                $comprobantePath = 'comprobantes/' . $filename;
            }

            DonacionesDinero::updateOrCreate(
                ['id_donacion' => $id],
                [
                    'monto' => $validated['monto'],
                    'moneda' => $validated['divisa'] ?? 'BOB',
                    'metodo_pago' => 'transferencia',
                    'referencia_pago' => $validated['numero_cuenta'] ?? null,
                    'comprobante_imagen' => $comprobantePath,
                    'estado' => $validated['estado_validacion'] ?? 'pendiente',
                    'entidad_bancaria' => $validated['nombre_cuenta'] ?? null,
                ]
            );

            return response()->json([
                'message' => 'Donación en dinero actualizada exitosamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error actualizando donación en dinero: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al actualizar donación en dinero',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/donaciones-en-dinero
     * Create a money donation directly
     */
    public function createMoneyDonation(Request $request)
    {
        $validated = $request->validate([
            'id_donacion' => 'required|exists:inventario.donaciones,id_donacion',
            'monto' => 'required|numeric|min:0.01',
            'moneda' => 'nullable|string|max:10',
            'metodo_pago' => 'nullable|string|max:30',
            'referencia_pago' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:20',
        ]);

        try {
            $donacionDinero = DonacionesDinero::create([
                'id_donacion' => $validated['id_donacion'],
                'monto' => $validated['monto'],
                'moneda' => $validated['moneda'] ?? 'BOB',
                'metodo_pago' => $validated['metodo_pago'] ?? 'Pasarela',
                'referencia_pago' => $validated['referencia_pago'] ?? null,
                'estado' => $validated['estado'] ?? 'pendiente',
            ]);

            return response()->json([
                'id' => $donacionDinero->id_donacion_dinero,
                'message' => 'Donación en dinero creada exitosamente'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creando donación en dinero: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al crear donación en dinero',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/donantes/{id}/donaciones
     */
    public function getByDonante($donanteId)
    {
        try {
            $donaciones = Donacione::where('id_donante', $donanteId)
                ->with(['detalles.producto', 'campana'])
                ->orderBy('fecha', 'desc')
                ->get();

            return response()->json($donaciones, 200);

        } catch (\Exception $e) {
            Log::error('Error obteniendo donaciones: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener donaciones'], 500);
        }
    }

    /**
     * GET /api/donaciones-en-dinero/getAllById/{id}
     */
    public function getMoneyDonationsByDonante($donanteId)
    {
        try {
            $donaciones = Donacione::where('id_donante', $donanteId)
                ->where('tipo', 'dinero')
                ->with('dinero')
                ->orderBy('fecha', 'desc')
                ->get();

            return response()->json($donaciones, 200);

        } catch (\Exception $e) {
            Log::error('Error obteniendo donaciones en dinero: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener donaciones en dinero'], 500);
        }
    }

    /**
     * PATCH /api/donaciones/estado/{id}
     */
    public function updateEstado($id, Request $request)
    {
        $validated = $request->validate([
            'estado_validacion' => 'required|string',
        ]);

        try {
            $donacion = Donacione::findOrFail($id);
            
            if ($donacion->dinero) {
                $donacion->dinero->update([
                    'estado' => $validated['estado_validacion']
                ]);
            }

            return response()->json(['message' => 'Estado actualizado exitosamente'], 200);

        } catch (\Exception $e) {
            Log::error('Error actualizando estado: ' . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar estado'], 500);
        }
    }

    /**
     * GET /api/donaciones/dinero
     */
    public function getAllMoneyDonations()
    {
        try {
            $donaciones = Donacione::where('tipo', 'dinero')
                ->with(['dinero', 'donante', 'campana'])
                ->orderBy('fecha', 'desc')
                ->get();

            return response()->json($donaciones, 200);

        } catch (\Exception $e) {
            Log::error('Error obteniendo donaciones en dinero: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener donaciones en dinero'], 500);
        }
    }

    /**
     * GET /api/donaciones/especie
     */
    public function getAllInKindDonations()
    {
        try {
            $donaciones = Donacione::whereIn('tipo', ['especie', 'ropa'])
                ->with(['detalles.producto', 'donante', 'campana'])
                ->orderBy('fecha', 'desc')
                ->get();

            return response()->json($donaciones, 200);

        } catch (\Exception $e) {
            Log::error('Error obteniendo donaciones en especie: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener donaciones en especie'], 500);
        }
    }

    public function index()
    {
        return Donacione::with(['donante', 'campana'])->paginate(20);
    }

    public function show(string $id)
    {
        return Donacione::with(['detalles', 'dinero', 'donante'])->findOrFail($id);
    }

    /**
     * PUT/PATCH /api/inventario/donaciones/{donacione}
     */
    public function update(Request $request, string $donacione)
    {
        $donacion = Donacione::findOrFail($donacione);

        $validated = $request->validate([
            'tipo' => 'sometimes|in:dinero,especie,ropa',
            'fecha' => 'sometimes|date',
            'id_donante' => 'sometimes|integer|exists:inventario.donantes,id_donante',
            'id_campana' => 'nullable|integer|exists:inventario.campanas,id_campana',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $donacion->update($validated);

        if ($donacion->tipo === 'dinero' && $request->has('monto')) {
            return $this->updateMoneyDonation($donacione, $request);
        }

        return response()->json([
            'message' => 'Donación actualizada',
            'data' => $donacion->fresh(['donante', 'campana']),
        ]);
    }

    /**
     * DELETE /api/inventario/donaciones/{donacione}
     */
    public function destroy(string $donacione)
    {
        $donacion = Donacione::findOrFail($donacione);
        $donacion->delete();

        return response()->json(['message' => 'Donación eliminada correctamente']);
    }

    /**
     * POST /api/upload-comprobante
     * Upload payment receipt image
     */
    public function uploadComprobante(Request $request)
    {
        $request->validate([
            'comprobante' => 'required|image|mimes:jpeg,png,jpg,gif,pdf|max:5120'
        ]);

        try {
            if ($request->hasFile('comprobante')) {
                $file = $request->file('comprobante');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/comprobantes'), $filename);
                
                return response()->json([
                    'path' => 'images/comprobantes/' . $filename,
                    'url' => url('images/comprobantes/' . $filename),
                    'message' => 'Comprobante subido exitosamente'
                ], 200);
            }

            return response()->json([
                'error' => 'No se recibió ningún archivo'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error al subir comprobante: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al subir comprobante',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/donaciones/especie/{id}/detalle
     * Get detailed information of in-kind donation including location and packages
     */
    public function getInKindDonationDetail($id)
    {
        try {
            $donacion = Donacione::with([
                'donante:id_donante,nombre,email,telefono,tipo',
                'campana:id_campana,nombre,descripcion',
                'detalles.producto:id_producto,nombre,descripcion,unidad_medida',
                'detalles.ubicaciones.espacio.estante.almacene',
                'detalles.paqueteDetalles.paquete'
            ])
            ->where('id_donacion', $id)
            ->whereIn('tipo', ['especie', 'ropa'])
            ->first();

            if (!$donacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Donación en especie no encontrada'
                ], 404);
            }

            // Formatear la respuesta
            $response = [
                'id_donacion' => $donacion->id_donacion,
                'tipo' => $donacion->tipo,
                'fecha' => $donacion->fecha,
                'observaciones' => $donacion->observaciones,
                'donante' => $donacion->donante,
                'campana' => $donacion->campana,
                'detalles' => $donacion->detalles->map(function ($detalle) {
                    return [
                        'id_detalle_donacion' => $detalle->id_detalle_donacion,
                        'cantidad' => $detalle->cantidad,
                        'unidad_medida' => $detalle->unidad_medida,
                        'producto' => $detalle->producto,
                        'ubicaciones' => $detalle->ubicaciones->map(function ($ubicacion) {
                            $espacio = $ubicacion->espacio;
                            $estante = $espacio ? $espacio->estante : null;
                            $almacen = $estante ? $estante->almacene : null;
                            
                            return [
                                'id_ubicacion' => $ubicacion->id_ubicacion,
                                'espacio' => [
                                    'id_espacio' => $espacio->id_espacio ?? null,
                                    'codigo_espacio' => $espacio->codigo_espacio ?? null,
                                ],
                                'estante' => [
                                    'id_estante' => $estante->id_estante ?? null,
                                    'codigo_estante' => $estante->codigo_estante ?? null,
                                ],
                                'almacen' => [
                                    'id_almacen' => $almacen->id_almacen ?? null,
                                    'nombre' => $almacen->nombre ?? null,
                                    'direccion' => $almacen->direccion ?? null,
                                ]
                            ];
                        }),
                        'paquetes' => $detalle->paqueteDetalles->map(function ($paqueteDetalle) {
                            return [
                                'id_paquete' => $paqueteDetalle->paquete->id_paquete ?? null,
                                'codigo_paquete' => $paqueteDetalle->paquete->codigo_paquete ?? null,
                                'fecha_creacion' => $paqueteDetalle->paquete->fecha_creacion ?? null,
                                'cantidad_en_paquete' => $paqueteDetalle->cantidad_usada ?? null,
                            ];
                        })
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $response
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error obteniendo detalle de donación en especie: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalle de donación',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}









