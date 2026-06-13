<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\SolicitudesRecoleccion;
use Modules\Inventario\Models\Donante;
use Modules\Inventario\Models\Usuario;
use Modules\Inventario\Models\Producto;
use Modules\Inventario\Models\Almacene;
use Modules\Inventario\Models\Campana;
use Modules\Inventario\Models\PuntosRecoleccion;
use Modules\Inventario\Models\Donacione;
use Modules\Inventario\Models\DonacionDetalle;
use Modules\Inventario\Models\UbicacionesDonacione;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\SolicitudesRecoleccionRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SolicitudesRecoleccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $solicitudesRecoleccions = SolicitudesRecoleccion::with(['donante', 'usuario'])
            ->orderByDesc('fecha_programada')
            ->get();

        return view('inventario::solicitudes-recoleccion.index', compact('solicitudesRecoleccions'))
            ->with('i', 0);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $solicitudesRecoleccion = new SolicitudesRecoleccion();
        $donantes = Donante::all();
        $usuarios = Usuario::recolectoresActivos()->get();

        return view('inventario::solicitudes-recoleccion.create', compact('solicitudesRecoleccion', 'donantes', 'usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SolicitudesRecoleccionRequest $request): RedirectResponse
    {
        SolicitudesRecoleccion::create($request->validated());

        return Redirect::route('inventario.solicitudes-recoleccions.index')
            ->with('success', 'Solicitud de recolección creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $solicitudesRecoleccion = SolicitudesRecoleccion::find($id);

        return view('inventario::solicitudes-recoleccion.show', compact('solicitudesRecoleccion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $solicitudesRecoleccion = SolicitudesRecoleccion::find($id);
        $donantes = Donante::all();
        $usuarios = Usuario::recolectoresActivos()->get();
        
        // Data for donation form
        $productos = Producto::pluck('nombre', 'id_producto');
        $almacenes = Almacene::pluck('nombre', 'id_almacen');
        $campanas = Campana::pluck('nombre', 'id_campana');
        $puntos = PuntosRecoleccion::pluck('nombre', 'id_punto');
        
        // Get product units
        $productosUnidades = Producto::pluck('unidad_medida', 'id_producto');

        return view('inventario::solicitudes-recoleccion.edit', compact(
            'solicitudesRecoleccion', 
            'donantes', 
            'usuarios',
            'productos',
            'almacenes',
            'campanas',
            'puntos',
            'productosUnidades'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SolicitudesRecoleccionRequest $request, SolicitudesRecoleccion $solicitudesRecoleccion): RedirectResponse
    {
        DB::beginTransaction();
        
        try {
            $solicitudesRecoleccion->update($request->validated());
            
            // If estado is completada and crear_donacion is 1, create donation
            if ($request->input('estado') === 'completada' && $request->input('crear_donacion') == '1') {
                // Create donation
                $donacion = Donacione::create([
                    'id_donante' => $solicitudesRecoleccion->id_donante,
                    'tipo' => 'especie',
                    'id_campana' => $request->input('donacion_id_campana'),
                    'id_punto_recoleccion' => $request->input('donacion_id_punto_recoleccion'),
                    'observaciones' => $request->input('donacion_observaciones'),
                ]);
                
                // Create donation details and ubicaciones
                $detalles = $request->input('donacion_detalles', []);
                foreach ($detalles as $detalle) {
                    if (!empty($detalle['id_producto']) && !empty($detalle['cantidad']) && !empty($detalle['id_espacio'])) {
                        $detalleCreado = DonacionDetalle::create([
                            'id_donacion' => $donacion->id_donacion,
                            'id_producto' => $detalle['id_producto'],
                            'cantidad' => $detalle['cantidad'],
                            'unidad_medida' => $detalle['unidad_medida'] ?? 'unidad',
                        ]);
                        
                        // Create ubicacion
                        UbicacionesDonacione::create([
                            'id_detalle_donacion' => $detalleCreado->id_detalle_donacion,
                            'id_espacio' => $detalle['id_espacio'],
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return Redirect::route('inventario.solicitudes-recoleccions.index')
                ->with('success', 'Solicitud de recolección actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return Redirect::back()
                ->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy($id): RedirectResponse
    {
        SolicitudesRecoleccion::find($id)->delete();

        return Redirect::route('inventario.solicitudes-recoleccions.index')
            ->with('success', 'Solicitud de recolección eliminada exitosamente.');
    }
}







