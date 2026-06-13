<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\PuntosRecoleccion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\PuntosRecoleccionRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PuntosRecoleccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $puntosRecoleccions = PuntosRecoleccion::orderBy('nombre')->get();

        return view('inventario::puntos-recoleccion.index', compact('puntosRecoleccions'))
            ->with('i', 0);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $puntosRecoleccion = new PuntosRecoleccion();

        return view('inventario::puntos-recoleccion.create', compact('puntosRecoleccion'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PuntosRecoleccionRequest $request)
    {
        $punto = PuntosRecoleccion::create($request->validated());

        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Punto de recolección creado exitosamente',
                'punto' => $punto
            ]);
        }

        return Redirect::route('inventario.puntos-recoleccion.index')
            ->with('success', 'Punto de recolección creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $puntosRecoleccion = PuntosRecoleccion::find($id);

        return view('inventario::puntos-recoleccion.show', compact('puntosRecoleccion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $puntosRecoleccion = PuntosRecoleccion::find($id);

        return view('inventario::puntos-recoleccion.edit', compact('puntosRecoleccion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PuntosRecoleccionRequest $request, PuntosRecoleccion $puntosRecoleccion): RedirectResponse
    {
        $puntosRecoleccion->update($request->validated());

        return Redirect::route('inventario.puntos-recoleccion.index')
            ->with('success', 'Punto de recolección actualizado exitosamente.');
    }

    public function destroy($id): RedirectResponse
    {
        PuntosRecoleccion::find($id)->delete();

        return Redirect::route('inventario.puntos-recoleccion.index')
            ->with('success', 'Punto de recolección eliminado exitosamente.');
    }
}







