<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\Espacio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\EspacioRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class EspacioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $espacios = Espacio::with([
            'estante.almacene',
            'ubicacionesDonaciones.detalle.producto',
            'ubicacionesDonaciones.detalle.donacion'
        ])->orderBy('codigo_espacio')->get();

        return view('inventario::espacio.index', compact('espacios'))
            ->with('i', 0);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $espacio = new Espacio();

        // load estantes for FK select
        $estantes = \Modules\Inventario\Models\Estante::pluck('codigo_estante', 'id_estante');

        return view('inventario::espacio.create', compact('espacio', 'estantes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EspacioRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Auto-generate codigo_espacio
        $idEstante = $data['id_estante'];

        // Get the count of existing espacios for this estante
        $count = Espacio::where('id_estante', $idEstante)->count() + 1;

        // Generate code like: EST1-ESP001, EST1-ESP002, etc.
        $data['codigo_espacio'] = 'EST' . $idEstante . '-ESP' . str_pad($count, 3, '0', STR_PAD_LEFT);

        Espacio::create($data);

        return Redirect::route('inventario.espacio.index')
            ->with('success', 'Espacio creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $espacio = Espacio::with([
            'estante.almacene'
        ])->find($id);

        return view('inventario::espacio.show', compact('espacio'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $espacio = Espacio::find($id);

        // load estantes for FK select
        $estantes = \Modules\Inventario\Models\Estante::pluck('codigo_estante', 'id_estante');

        return view('inventario::espacio.edit', compact('espacio', 'estantes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EspacioRequest $request, Espacio $espacio): RedirectResponse
    {
        $espacio->update($request->validated());

        return Redirect::route('inventario.espacio.index')
            ->with('success', 'Espacio actualizado exitosamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Espacio::find($id)->delete();

        return Redirect::route('inventario.espacio.index')
            ->with('success', 'Espacio eliminado exitosamente.');
    }

    public function toggleStatus($id): RedirectResponse
    {
        $espacio = Espacio::findOrFail($id);
        $espacio->estado = $espacio->estado === 'lleno' ? 'disponible' : 'lleno';
        $espacio->save();

        return back()->with('success', 'Estado del espacio actualizado correctamente.');
    }
}







