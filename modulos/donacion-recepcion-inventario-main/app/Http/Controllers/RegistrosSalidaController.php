<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\RegistrosSalida;
use Modules\Inventario\Models\Paquete;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\RegistrosSalidaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RegistrosSalidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $registrosSalidas = RegistrosSalida::with('paquete')->orderBy('fecha_salida', 'desc')->paginate();

        return view('inventario::registros-salida.index', compact('registrosSalidas'))
            ->with('i', ($request->input('page', 1) - 1) * $registrosSalidas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $registrosSalida = new RegistrosSalida();
        // Excluir paquetes que ya tienen registro de salida
        $paquetesConSalida = RegistrosSalida::pluck('id_paquete')->toArray();
        $paquetes = Paquete::where('estado', '!=', 'Entregado')
            ->whereNotIn('id_paquete', $paquetesConSalida)
            ->get();
        return view('inventario::registros-salida.create', compact('registrosSalida', 'paquetes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RegistrosSalidaRequest $request): RedirectResponse
    {
        RegistrosSalida::create($request->validated());

        return Redirect::route('inventario.registros-salida.index')
            ->with('success', 'Registro de salida creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $registrosSalida = RegistrosSalida::with('paquete')->find($id);

        return view('inventario::registros-salida.show', compact('registrosSalida'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $registrosSalida = RegistrosSalida::find($id);
        $paquetes = Paquete::all();
        return view('inventario::registros-salida.edit', compact('registrosSalida', 'paquetes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RegistrosSalidaRequest $request, $id): RedirectResponse
    {
        $registrosSalida = RegistrosSalida::findOrFail($id);
        $registrosSalida->update($request->validated());

        return Redirect::route('inventario.registros-salida.index')
            ->with('success', 'Registro de salida actualizado exitosamente.');
    }

    public function destroy($id): RedirectResponse
    {
        RegistrosSalida::find($id)->delete();

        return Redirect::route('inventario.registros-salida.index')
            ->with('success', 'Registro de salida eliminado exitosamente.');
    }
}







