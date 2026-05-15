<?php

namespace Modules\Rescate\Http\Controllers;

use Modules\Rescate\Models\IncidentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Rescate\Http\Requests\IncidentTypeRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class IncidentTypeController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden gestionar tipos de incidentes
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $incidentTypes = IncidentType::paginate();

        return view('incident-type.index', compact('incidentTypes'))
            ->with('i', ($request->input('page', 1) - 1) * $incidentTypes->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $incidentType = new IncidentType();

        return view('incident-type.create', compact('incidentType'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IncidentTypeRequest $request): RedirectResponse
    {
        IncidentType::create($request->validated());

        return Redirect::route('rescate.incident-types.index')
            ->with('success', 'Tipo de incidente creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $incidentType = IncidentType::findOrFail($id);

        return view('incident-type.show', compact('incidentType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $incidentType = IncidentType::findOrFail($id);

        return view('incident-type.edit', compact('incidentType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IncidentTypeRequest $request, IncidentType $incidentType): RedirectResponse
    {
        $incidentType->update($request->validated());

        return Redirect::route('rescate.incident-types.index')
            ->with('success', 'Tipo de incidente actualizado correctamente.');
    }

    public function destroy($id): RedirectResponse
    {
        IncidentType::findOrFail($id)->delete();

        return Redirect::route('rescate.incident-types.index')
            ->with('success', 'Tipo de incidente eliminado correctamente.');
    }
}
