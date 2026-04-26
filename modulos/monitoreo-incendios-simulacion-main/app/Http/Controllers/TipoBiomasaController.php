<?php

namespace App\Http\Controllers;

use App\Models\TipoBiomasa;
use App\Http\Requests\TipoBiomasaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipoBiomasaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $tipoBiomasas = TipoBiomasa::paginate();

        return view('tipo-biomasa.index', compact('tipoBiomasas'))
            ->with('i', ($request->input('page', 1) - 1) * $tipoBiomasas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $tipoBiomasa = new TipoBiomasa();

        return view('tipo-biomasa.create', compact('tipoBiomasa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TipoBiomasaRequest $request): RedirectResponse
    {
        TipoBiomasa::create($request->validated());

        return redirect()->route('tipo-biomasas.index')
            ->with('success', 'Tipo de Biomasa creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $tipoBiomasa = TipoBiomasa::find($id);

        return view('tipo-biomasa.show', compact('tipoBiomasa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $tipoBiomasa = TipoBiomasa::find($id);

        return view('tipo-biomasa.edit', compact('tipoBiomasa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TipoBiomasaRequest $request, TipoBiomasa $tipoBiomasa): RedirectResponse
    {
        $tipoBiomasa->update($request->validated());

        return redirect()->route('tipo-biomasas.index')
            ->with('success', 'Tipo de Biomasa actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        TipoBiomasa::find($id)->delete();

        return redirect()->route('tipo-biomasas.index')
            ->with('success', 'Tipo de Biomasa eliminado exitosamente');
    }
}
