<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\Estante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\EstanteRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class EstanteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $estantes = Estante::paginate();

        return view('inventario::estante.index', compact('estantes'))
            ->with('i', ($request->input('page', 1) - 1) * $estantes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $estante = new Estante();
        $returnUrl = $request->query('return_url');
        $idAlmacen = $request->query('id_almacen');

        // load almacenes for FK select
        $almacenes = \Modules\Inventario\Models\Almacene::pluck('nombre', 'id_almacen');

        return view('inventario::estante.create', compact('estante', 'almacenes', 'returnUrl', 'idAlmacen'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EstanteRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Auto-generate codigo_estante
        $idAlmacen = $data['id_almacen'];

        // Get the count of existing estantes for this almacen
        $count = Estante::where('id_almacen', $idAlmacen)->count() + 1;

        // Generate code like: ALM1-EST001, ALM1-EST002, etc.
        $data['codigo_estante'] = 'ALM' . $idAlmacen . '-EST' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $estante = Estante::create($data);

        // Create spaces based on rows and columns
        $filas = (int) $request->input('filas');
        $columnas = (int) $request->input('columnas');

        for ($i = 1; $i <= $filas; $i++) {
            for ($j = 1; $j <= $columnas; $j++) {
                \Modules\Inventario\Models\Espacio::create([
                    'id_estante' => $estante->id_estante,
                    'codigo_espacio' => 'F' . $i . '-C' . $j,
                ]);
            }
        }

        // Check if there's a return URL
        if ($request->has('return_url') && $request->input('return_url')) {
            return Redirect::to($request->input('return_url'))
                ->with('success', 'Estante y espacios creados exitosamente.')
                ->with('new_estante_id', $estante->id_estante);
        }

        return Redirect::route('inventario.estante.index')
            ->with('success', 'Estante y ' . ($filas * $columnas) . ' espacios creados exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $estante = Estante::with([
            'almacene',
            'espacios.ubicacionesDonaciones.detalle.producto',
            'espacios.ubicacionesDonaciones.detalle.donacion'
        ])->find($id);

        // Group products by name within each space
        foreach ($estante->espacios as $espacio) {
            // Ensure relationships are loaded
            $espacio->load([
                'ubicacionesDonaciones.detalle.producto',
                'ubicacionesDonaciones.detalle.donacion.donante'
            ]);

            $grouped = [];

            foreach ($espacio->ubicacionesDonaciones as $ubicacion) {
                if ($ubicacion->detalle && $ubicacion->detalle->producto) {
                    $producto = $ubicacion->detalle->producto;
                    $productoNombre = $producto->nombre;

                    // Initialize group if it doesn't exist
                    if (!isset($grouped[$productoNombre])) {
                        $grouped[$productoNombre] = [
                            'producto' => $producto,
                            'cantidad_total' => 0,
                            'unidad_medida' => $producto->unidad_medida,
                            'donaciones' => []
                        ];
                    }

                    // Add quantity
                    $grouped[$productoNombre]['cantidad_total'] += $ubicacion->cantidad_ubicada;

                    // Add donation detail
                    $grouped[$productoNombre]['donaciones'][] = [
                        'id_donacion' => $ubicacion->detalle->donacion->id_donacion ?? null,
                        'cantidad' => $ubicacion->cantidad_ubicada,
                        'fecha' => $ubicacion->detalle->donacion->fecha ?? null,
                        'descripcion' => $ubicacion->detalle->descripcion ?? null,
                        'donante' => $ubicacion->detalle->donacion->donante->nombre ?? 'N/A'
                    ];
                }
            }

            // Attach grouped products to espacio
            $espacio->productosAgrupados = $grouped;
        }

        return view('inventario::estante.show', compact('estante'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $estante = Estante::find($id);

        // load almacenes for FK select
        $almacenes = \Modules\Inventario\Models\Almacene::pluck('nombre', 'id_almacen');

        return view('inventario::estante.edit', compact('estante', 'almacenes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EstanteRequest $request, Estante $estante): RedirectResponse
    {
        $estante->update($request->validated());

        return Redirect::route('inventario.estante.index')
            ->with('success', 'Estante actualizado exitosamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Estante::find($id)->delete();

        return Redirect::route('inventario.estante.index')
            ->with('success', 'Estante eliminado exitosamente.');
    }

    public function getEspacios($id)
    {
        return response()->json(Estante::find($id)->espacios()->where('estado', 'disponible')->select('id_espacio', 'codigo_espacio')->get());
    }
}







