<?php

namespace Modules\Rescate\Http\Controllers;

use Modules\Rescate\Models\Animal;
use Illuminate\Support\Facades\DB;
use Modules\Rescate\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Rescate\Http\Requests\AnimalRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $animals = Animal::paginate();

        return view('animal.index', compact('animals'))
            ->with('i', ($request->input('page', 1) - 1) * $animals->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $animal = new Animal();
        // Listar reportes aprobados
        $reports = Report::query()
            ->where('aprobado', 1)
            ->orderByDesc('reports.id')
            ->get(['reports.id']);

        return view('animal.create', compact('animal','reports'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnimalRequest $request): RedirectResponse
    {
        Animal::create($request->validated());

        return Redirect::route('rescate.animals.index')
            ->with('success', 'Animal creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $animal = Animal::findOrFail($id);

        return view('animal.show', compact('animal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $animal = Animal::findOrFail($id);
        // Reportes aprobados
        $reports = Report::query()
            ->where('aprobado', 1)
            ->leftJoin('animals', 'animals.reporte_id', '=', 'reports.id')
            ->groupBy('reports.id')
            ->orderByDesc('reports.id')
            ->get(['reports.id'])
            ->keyBy('id');
        // Asegurar que el reporte actual esté disponible en el select aunque esté lleno
        if ($animal?->reporte_id && !$reports->has($animal->reporte_id)) {
            $reports->put($animal->reporte_id, (object)['id' => $animal->reporte_id]);
        }
        $reports = $reports->values();

        return view('animal.edit', compact('animal','reports'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AnimalRequest $request, Animal $animal): RedirectResponse
    {
        $animal->update($request->validated());

        return Redirect::route('rescate.animals.index')
            ->with('success', 'Animal actualizado correctamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Animal::findOrFail($id)->delete();

        return Redirect::route('rescate.animals.index')
            ->with('success', 'Animal eliminado correctamente.');
    }
}
