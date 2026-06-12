<?php

namespace Modules\Inventario\Http\Controllers;

use App\Support\OwnershipScope;
use Modules\Inventario\Http\Requests\DonacioneRequest;
use Modules\Inventario\Models\Donacione;
use Modules\Inventario\Models\DonacionesDinero;
use Modules\Inventario\Models\DonacionDetalle;
use Modules\Inventario\Models\UbicacionesDonacione;
use Modules\Inventario\Models\Donante;
use Modules\Inventario\Models\Campana;
use Modules\Inventario\Models\PuntosRecoleccion;
use Modules\Inventario\Models\Producto;
use Modules\Inventario\Models\Espacio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DonacioneController extends Controller
{
    public function index(Request $request): View
    {
        $baseQuery = OwnershipScope::scopedDonacionesQuery(auth()->user());
        $donaciones = (clone $baseQuery)->with(['donante'])->paginate();

        $totalDonaciones = (clone $baseQuery)->count();
        $donacionesDinero = (clone $baseQuery)->where('tipo', 'dinero')->count();
        $donacionesEspecie = (clone $baseQuery)->where('tipo', 'especie')->count();

        $montoTotal = DB::connection('inventario')->table('donaciones_dinero')
            ->join('donaciones', 'donaciones_dinero.id_donacion', '=', 'donaciones.id_donacion')
            ->when(auth()->user()?->hasRole('Donante'), function ($query) {
                $donanteId = OwnershipScope::inventarioDonanteId(auth()->user());
                if ($donanteId) {
                    $query->where('donaciones.id_donante', $donanteId);
                }
            })
            ->sum('donaciones_dinero.monto');

        return view('inventario::donaciones.index', compact('donaciones', 'totalDonaciones', 'donacionesDinero', 'donacionesEspecie', 'montoTotal'))
            ->with('i', ($request->input('page', 1) - 1) * $donaciones->perPage());
    }

    public function create(): View
    {
        OwnershipScope::assertCanMutateDonacion(auth()->user());

        $donanteProfile = null;
        if (auth()->user()?->hasRole('Donante')) {
            $donanteProfile = OwnershipScope::ensureInventarioDonanteProfile(auth()->user());
        }

        $donantes = auth()->user()?->hasRole('Donante')
            ? collect([$donanteProfile->id_donante => $donanteProfile->nombre])
            : Donante::pluck('nombre', 'id_donante');
        $campanas = Campana::pluck('nombre', 'id_campana');
        $puntos = PuntosRecoleccion::pluck('nombre', 'id_punto');
        $productos = Producto::activos()->ordenPrioridad()->pluck('nombre', 'id_producto');
        $espacios = Espacio::where('estado', 'disponible')->pluck('codigo_espacio', 'id_espacio');

        // Get products with their unit measurements for auto-fill
        $productosData = Producto::with('categoriaProducto')->select('id_producto', 'nombre', 'unidad_medida', 'id_categoria')->get();
        $productosUnidades = $productosData->pluck('unidad_medida', 'id_producto')->toArray();

        $productosCategorias = $this->mapProductosCategorias($productosData);

        // Provide an empty model instance so the form can safely access $donacion
        $donacion = new Donacione();
        $almacenes = \Modules\Inventario\Models\Almacene::pluck('nombre', 'id_almacen');
        $categorias = \Modules\Inventario\Models\CategoriasProducto::activas()->orderBy('nombre')->pluck('nombre', 'id_categoria');

        // Find the "Central" warehouse as default
        $almacenCentral = \Modules\Inventario\Models\Almacene::where('nombre', 'LIKE', '%Central%')->first();
        $defaultAlmacenId = $almacenCentral ? $almacenCentral->id_almacen : null;

        // Get tallas for clothing products
        $tallas = \Modules\Inventario\Models\Talla::pluck('talla', 'id_talla');

        return view('inventario::donaciones.create', compact('donacion', 'donantes', 'campanas', 'puntos', 'productos', 'espacios', 'productosUnidades', 'productosCategorias', 'almacenes', 'categorias', 'defaultAlmacenId', 'tallas'));
    }

    public function store(DonacioneRequest $request): RedirectResponse
    {
        OwnershipScope::assertCanMutateDonacion(auth()->user());

        $data = $request->validated();

        \Log::info('=== INICIO STORE DONACION ===');
        \Log::info('Datos validados:', $data);

        try {
            DB::transaction(function () use ($data, $request) {
                \Log::info('Iniciando transacción...');

                $donacion = Donacione::create([
                    'id_donante' => $data['id_donante'],
                    'tipo' => $data['tipo'],
                    'id_campana' => $data['id_campana'] ?? null,
                    'id_punto_recoleccion' => $data['id_punto_recoleccion'] ?? null,
                    'observaciones' => $data['observaciones'] ?? null,
                    'fecha' => now(),
                    'ci_usuario_registro' => auth()->user()->ci ?? null,
                ]);

                \Log::info('Donación creada:', ['id' => $donacion->id_donacion, 'tipo' => $donacion->tipo]);

                if ($data['tipo'] === 'dinero') {
                    \Log::info('Creando registro de dinero...');

                    $referenciaPago = null;

                    // Manejar la subida de imagen de referencia de pago
                    if ($request->hasFile('referencia_pago_file')) {
                        $file = $request->file('referencia_pago_file');
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('images/comprobantes'), $filename);
                        $referenciaPago = 'images/comprobantes/' . $filename;
                    }

                    \Log::info('Datos dinero:', [
                        'id_donacion' => $donacion->id_donacion,
                        'monto' => $data['monto'] ?? 'NULL',
                        'moneda' => $data['moneda'] ?? 'NULL',
                        'metodo_pago' => $data['metodo_pago'] ?? 'NULL',
                        'referencia_pago' => $referenciaPago ?? 'NULL',
                    ]);

                    DonacionesDinero::create([
                        'id_donacion' => $donacion->id_donacion,
                        'monto' => $data['monto'],
                        'moneda' => $data['moneda'] ?? null,
                        'metodo_pago' => $data['metodo_pago'] ?? null,
                        'referencia_pago' => $referenciaPago,
                    ]);

                    \Log::info('Registro de dinero creado exitosamente');
                } else {
                    \Log::info('Creando detalles de productos...', ['cantidad_detalles' => count($data['detalles'] ?? [])]);

                    foreach ($data['detalles'] as $index => $det) {
                        \Log::info("Procesando detalle #{$index}:", $det);

                        $detalle = DonacionDetalle::create([
                            'id_donacion' => $donacion->id_donacion,
                            'id_producto' => $det['id_producto'],
                            'cantidad' => (int) $det['cantidad'],
                            'unidad_medida' => $det['unidad_medida'] ?? null,
                            'descripcion' => $det['descripcion'] ?? null,
                            'id_talla' => $det['id_talla'] ?? null,
                            'id_genero' => $det['id_genero'] ?? null,
                            'fecha_caducidad' => $det['fecha_caducidad'] ?? null,
                        ]);

                        \Log::info("Detalle #{$index} creado:", ['id' => $detalle->id_detalle]);

                        // Only create location if espacio is provided
                        if (!empty($det['id_espacio'])) {
                            $espacio = Espacio::find($det['id_espacio']);
                            if ($espacio && $espacio->estado === 'lleno') {
                                throw new \Exception("El espacio {$espacio->codigo_espacio} está marcado como lleno y no puede recibir más productos.");
                            }

                            UbicacionesDonacione::create([
                                'id_detalle' => $detalle->id_detalle,
                                'id_espacio' => $det['id_espacio'],
                                'fecha_ingreso' => now(),
                                'cantidad_ubicada' => (int) $det['cantidad'],
                            ]);

                            \Log::info("Ubicación para detalle #{$index} creada");
                        } else {
                            \Log::info("No se creó ubicación para detalle #{$index} - espacio no proporcionado");
                        }
                    }
                }

                \Log::info('Transacción completada exitosamente');
            });

            \Log::info('=== FIN STORE DONACION (SUCCESS) ===');
            return Redirect::route('inventario.donaciones.index')->with('success', 'Donación creada correctamente.');

        } catch (\Throwable $e) {
            \Log::error('=== ERROR EN STORE DONACION ===');
            \Log::error('Mensaje: ' . $e->getMessage());
            \Log::error('Archivo: ' . $e->getFile() . ':' . $e->getLine());
            \Log::error('Trace: ' . $e->getTraceAsString());

            return back()->withInput()->withErrors(['error' => 'Ocurrió un error al crear la donación: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $donacion = Donacione::with(['detalles.producto', 'dinero', 'donante'])->find($id);

        if (! $donacion) {
            return redirect()->route('inventario.donaciones.index')->with('error', 'Donación no encontrada.');
        }

        OwnershipScope::assertCanAccessDonacion(auth()->user(), $donacion);

        return view('inventario::donaciones.show', compact('donacion'));
    }

    public function edit($id): View
    {
        $donacion = Donacione::with(['detalles'])->findOrFail($id);
        OwnershipScope::assertCanMutateDonacion(auth()->user(), $donacion);

        if (auth()->user()?->hasRole('Donante')) {
            abort(403, 'Los donantes no pueden editar donaciones registradas.');
        }

        $donantes = Donante::pluck('nombre', 'id_donante');
        $campanas = Campana::pluck('nombre', 'id_campana');
        $puntos = PuntosRecoleccion::pluck('nombre', 'id_punto');
        $productos = Producto::activos()->ordenPrioridad()->pluck('nombre', 'id_producto');
        $espacios = Espacio::where('estado', 'disponible')->pluck('codigo_espacio', 'id_espacio');

        // Get products with their unit measurements for auto-fill
        $productosData = Producto::with('categoriaProducto')->select('id_producto', 'nombre', 'unidad_medida', 'id_categoria')->get();
        $productosUnidades = $productosData->pluck('unidad_medida', 'id_producto')->toArray();

        $productosCategorias = $this->mapProductosCategorias($productosData);

        $almacenes = \Modules\Inventario\Models\Almacene::pluck('nombre', 'id_almacen');
        $categorias = \Modules\Inventario\Models\CategoriasProducto::activas()->orderBy('nombre')->pluck('nombre', 'id_categoria');

        // Find the "Central" warehouse as default
        $almacenCentral = \Modules\Inventario\Models\Almacene::where('nombre', 'LIKE', '%Central%')->first();
        $defaultAlmacenId = $almacenCentral ? $almacenCentral->id_almacen : null;

        // Get tallas for clothing products
        $tallas = \Modules\Inventario\Models\Talla::pluck('talla', 'id_talla');

        return view('inventario::donaciones.edit', compact('donacion', 'donantes', 'campanas', 'puntos', 'productos', 'espacios', 'productosUnidades', 'productosCategorias', 'almacenes', 'categorias', 'defaultAlmacenId', 'tallas'));
    }

    public function update(DonacioneRequest $request, Donacione $donacione): RedirectResponse
    {
        OwnershipScope::assertCanMutateDonacion(auth()->user(), $donacione);
        abort_if(auth()->user()?->hasRole('Donante'), 403);

        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $donacione, $request) {
                $donacione->update([
                    'id_donante' => $data['id_donante'],
                    'tipo' => $data['tipo'],
                    'id_campana' => $data['id_campana'] ?? null,
                    'id_punto_recoleccion' => $data['id_punto_recoleccion'] ?? null,
                    'observaciones' => $data['observaciones'] ?? null,
                ]);

                // borrar detalles y ubicaciones previas si aplica
                if ($donacione->detalles()->count()) {
                    foreach ($donacione->detalles as $oldDet) {
                        // eliminar ubicaciones relacionadas
                        UbicacionesDonacione::where('id_detalle', $oldDet->id_detalle)->delete();
                    }
                    $donacione->detalles()->delete();
                }

                // obtener donación dinero existente
                $dineroExistente = DonacionesDinero::where('id_donacion', $donacione->id_donacion)->first();

                if ($data['tipo'] === 'dinero') {
                    $referenciaPago = $dineroExistente?->referencia_pago;

                    // Manejar la subida de nueva imagen de referencia de pago
                    if ($request->hasFile('referencia_pago_file')) {
                        // Eliminar imagen anterior si existe
                        if ($referenciaPago && file_exists(public_path($referenciaPago))) {
                            unlink(public_path($referenciaPago));
                        }

                        $file = $request->file('referencia_pago_file');
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('images/comprobantes'), $filename);
                        $referenciaPago = 'images/comprobantes/' . $filename;
                    }

                    if ($dineroExistente) {
                        $dineroExistente->update([
                            'monto' => $data['monto'],
                            'moneda' => $data['moneda'] ?? null,
                            'metodo_pago' => $data['metodo_pago'] ?? null,
                            'referencia_pago' => $referenciaPago,
                        ]);
                    } else {
                        DonacionesDinero::create([
                            'id_donacion' => $donacione->id_donacion,
                            'monto' => $data['monto'],
                            'moneda' => $data['moneda'] ?? null,
                            'metodo_pago' => $data['metodo_pago'] ?? null,
                            'referencia_pago' => $referenciaPago,
                        ]);
                    }
                } else {
                    foreach ($data['detalles'] as $det) {
                        $detalle = DonacionDetalle::create([
                            'id_donacion' => $donacione->id_donacion,
                            'id_producto' => $det['id_producto'],
                            'cantidad' => (int) $det['cantidad'],
                            'unidad_medida' => $det['unidad_medida'] ?? null,
                            'descripcion' => $det['descripcion'] ?? null,
                            'id_talla' => $det['id_talla'] ?? null,
                            'id_genero' => $det['id_genero'] ?? null,
                            'fecha_caducidad' => $det['fecha_caducidad'] ?? null,
                        ]);

                        // Only create location if espacio is provided
                        if (!empty($det['id_espacio'])) {
                            $espacio = Espacio::find($det['id_espacio']);
                            if ($espacio && $espacio->estado === 'lleno') {
                                throw new \Exception("El espacio {$espacio->codigo_espacio} está marcado como lleno y no puede recibir más productos.");
                            }

                            UbicacionesDonacione::create([
                                'id_detalle' => $detalle->id_detalle,
                                'id_espacio' => $det['id_espacio'],
                                'fecha_ingreso' => now(),
                                'cantidad_ubicada' => (int) $det['cantidad'],
                            ]);
                        }
                    }
                }
            });

            return Redirect::route('inventario.donaciones.index')->with('success', 'Donación actualizada correctamente.');
        } catch (\Throwable $e) {
            \Log::error('Error actualizando donacion: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Ocurrió un error al actualizar la donación.']);
        }
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        abort_if(auth()->user()?->hasRole('Donante'), 403);

        $request->validate([
            'deleted_reason' => 'required|string|min:10|max:500',
        ], [
            'deleted_reason.required' => 'El motivo de eliminación es obligatorio.',
            'deleted_reason.min' => 'El motivo debe tener al menos 10 caracteres.',
            'deleted_reason.max' => 'El motivo no puede exceder 500 caracteres.',
        ]);

        $donacion = Donacione::find($id);
        if ($donacion) {
            OwnershipScope::assertCanMutateDonacion(auth()->user(), $donacion);
            abort_unless(
                auth()->user()?->hasAnyRole(['Administrador', 'Almacenero']),
                403
            );
            // Store deletion metadata and soft delete (no se borran los detalles para mantener trazabilidad)
            $donacion->deleted_reason = $request->deleted_reason;
            $donacion->deleted_by = auth()->user()->ci ?? null;
            $donacion->save();
            $donacion->delete(); // Soft delete - solo marca deleted_at
        }

        return Redirect::route('inventario.donaciones.index')->with('success', 'Donación eliminada correctamente.');
    }

    private function mapProductosCategorias($productosData): array
    {
        return $productosData->mapWithKeys(function ($producto) {
            if (! $producto->categoriaProducto) {
                return [$producto->id_producto => null];
            }

            return [$producto->id_producto => $producto->categoriaProducto->toDonacionMeta()];
        })->toArray();
    }
}







