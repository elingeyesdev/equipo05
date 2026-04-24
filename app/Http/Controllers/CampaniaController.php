<?php

namespace App\Http\Controllers;

use App\Models\Campania;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Importante para Auth::id()

class CampaniaController extends Controller
{
public function index()
    {
        // Listado con suma de recaudación (Monetaria + Confirmada/Asignada/Utilizada)
        $campanias = Campania::with(['creador'])
            ->withSum(['donaciones as montorecaudado_calculado' => function ($q) {
                $q->where('tipodonacion', 'Monetaria')
                  ->whereIn('estadoid', [2, 3, 4]); 
            }], 'monto')
            ->orderByDesc('campaniaid')
            ->get();

        return view('campanias.index', compact('campanias'));
    }

    public function create()
    {
        // No necesitamos enviar $usuarios, el creador eres tú (Auth)
        return view('campanias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'          => 'required|string|max:100',
            'descripcion'     => 'required|string',
            'fechainicio'     => 'required|date',
            'fechafin'        => 'nullable|date|after_or_equal:fechainicio',
            'metarecaudacion' => 'required|numeric|min:0',
            // 'montorecaudado' lo dejamos en 0 por defecto o lo calculamos
            'activa'          => 'boolean',
            'imagenurl'       => 'nullable|string|max:255',
            'fechacreacion'   => 'nullable|date',
        ]);

        $data = $request->only([
            'titulo','descripcion','fechainicio','fechafin','metarecaudacion',
            'activa','imagenurl','fechacreacion'
        ]);

        // ASIGNACIÓN AUTOMÁTICA DEL USUARIO LOGUEADO
        $data['usuarioidcreador'] = Auth::id();
        $data['montorecaudado']   = 0; // Inicia en 0

        Campania::create($data);

        return redirect()->route('campanias.index')->with('success', 'Campaña creada exitosamente.');
    }

    public function edit($id)
    {
        $campania = Campania::with('creador')->findOrFail($id);
        
        // No enviamos $usuarios. En el edit mostraremos al creador original como info.
        return view('campanias.edit', compact('campania'));
    }

    public function update(Request $request, $id)
    {
        $campania = Campania::findOrFail($id);

        $request->validate([
            'titulo'          => 'required|string|max:100',
            'descripcion'     => 'required|string',
            'fechainicio'     => 'required|date',
            'fechafin'        => 'nullable|date|after_or_equal:fechainicio',
            'metarecaudacion' => 'required|numeric|min:0',
            // 'montorecaudado' usualmente no se edita manual, se calcula con donaciones,
            // pero si lo permites manual, descomenta abajo:
            // 'montorecaudado' => 'nullable|numeric|min:0',
            'activa'          => 'boolean',
            'imagenurl'       => 'nullable|string|max:255',
            'fechacreacion'   => 'nullable|date',
        ]);

        $data = $request->only([
            'titulo','descripcion','fechainicio','fechafin','metarecaudacion',
            'activa','imagenurl','fechacreacion'
        ]);

        // Opcional: Si permites editar el monto manual
        if ($request->has('montorecaudado')) {
             $data['montorecaudado'] = $request->input('montorecaudado');
        }

        // NO actualizamos usuarioidcreador para mantener al dueño original
        $campania->update($data);

        return redirect()->route('campanias.index')->with('success', 'Campaña actualizada.');
    }

    public function destroy($id)
    {
        $campania = Campania::findOrFail($id);
        
        // Validar si tiene donaciones antes de borrar (opcional pero recomendado)
        if($campania->donaciones()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar una campaña que ya tiene donaciones.');
        }

        $campania->delete();
        return redirect()->route('campanias.index')->with('success', 'Campaña eliminada.');
    }
}