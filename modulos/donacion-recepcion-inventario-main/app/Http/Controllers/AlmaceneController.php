<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\Almacene;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\AlmaceneRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AlmaceneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $almacenes = Almacene::paginate();

        return view('inventario::almacene.index', compact('almacenes'))
            ->with('i', ($request->input('page', 1) - 1) * $almacenes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $almacene = new Almacene();
        $returnUrl = $request->query('return_url');

        return view('inventario::almacene.create', compact('almacene', 'returnUrl'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AlmaceneRequest $request): RedirectResponse
    {
        $almacene = Almacene::create($request->validated());

        // Check if there's a return URL
        if ($request->has('return_url') && $request->input('return_url')) {
            return Redirect::to($request->input('return_url'))
                ->with('success', 'Almacén creado exitosamente.')
                ->with('new_almacen_id', $almacene->id_almacen);
        }

        return Redirect::route('inventario.almacene.index')
            ->with('success', 'Almacén creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $almacene = Almacene::find($id);
        $estantes = $almacene->estantes()->with('espacios')->get();

        return view('inventario::almacene.show', compact('almacene', 'estantes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $almacene = Almacene::find($id);

        return view('inventario::almacene.edit', compact('almacene'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AlmaceneRequest $request, Almacene $almacene): RedirectResponse
    {
        $almacene->update($request->validated());

        return Redirect::route('inventario.almacene.index')
            ->with('success', 'Almacén actualizado exitosamente.');
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $almacene = Almacene::findOrFail($id);

            // Delete related entities in cascade
            foreach ($almacene->estantes as $estante) {
                // Delete espacios and their ubicaciones
                foreach ($estante->espacios as $espacio) {
                    // Delete ubicaciones relacionadas
                    \Modules\Inventario\Models\UbicacionesDonacione::where('id_espacio', $espacio->id_espacio)->delete();
                }
                // Delete espacios
                $estante->espacios()->delete();
            }

            // Delete estantes
            $almacene->estantes()->delete();

            // Finally delete the almacen
            $almacene->delete();

            return Redirect::route('inventario.almacene.index')
                ->with('success', 'Almacén eliminado correctamente.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting almacen: ' . $e->getMessage());
            return Redirect::back()
                ->with('error', 'Error al eliminar el almacén: ' . $e->getMessage());
        }
    }

    public function getEstantes($id)
    {
        return response()->json(Almacene::find($id)->estantes()->select('id_estante', 'codigo_estante', 'descripcion')->get());
    }
}







