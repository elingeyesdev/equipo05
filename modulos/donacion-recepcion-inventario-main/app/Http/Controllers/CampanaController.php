<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\Campana;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\CampanaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CampanaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $campanas = Campana::orderByDesc('fecha_inicio')->get();

        return view('inventario::campana.index', compact('campanas'))
            ->with('i', 0);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $campana = new Campana();

        return view('inventario::campana.create', compact('campana'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CampanaRequest $request)
    {
        $data = $request->validated();
        
        // Manejar la subida de imagen
        if ($request->hasFile('imagen_banner_file')) {
            $file = $request->file('imagen_banner_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/campanas'), $filename);
            $data['imagen_banner'] = 'images/campanas/' . $filename;
        }
        
        // Remover el campo del archivo ya que no existe en la BD
        unset($data['imagen_banner_file']);
        
        $campana = Campana::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Campaña creada exitosamente',
                'campana' => $campana
            ]);
        }

        return Redirect::route('inventario.campana.index')
            ->with('success', 'Campaña creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $campana = Campana::find($id);

        return view('inventario::campana.show', compact('campana'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $campana = Campana::find($id);

        return view('inventario::campana.edit', compact('campana'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CampanaRequest $request, Campana $campana): RedirectResponse
    {
        $data = $request->validated();
        
        // Manejar la subida de imagen
        if ($request->hasFile('imagen_banner_file')) {
            // Eliminar imagen anterior si existe
            if ($campana->imagen_banner && file_exists(public_path($campana->imagen_banner))) {
                unlink(public_path($campana->imagen_banner));
            }
            
            $file = $request->file('imagen_banner_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/campanas'), $filename);
            $data['imagen_banner'] = 'images/campanas/' . $filename;
        }
        
        // Remover el campo del archivo ya que no existe en la BD
        unset($data['imagen_banner_file']);
        
        $campana->update($data);

        return Redirect::route('inventario.campana.index')
            ->with('success', 'Campaña actualizada exitosamente.');
    }

    public function destroy($id): RedirectResponse
    {
        Campana::find($id)->delete();

        return Redirect::route('inventario.campana.index')
            ->with('success', 'Campaña eliminada exitosamente.');
    }
}







