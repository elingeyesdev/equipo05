<?php

namespace App\Http\Controllers;

use App\Models\Campania;
use App\Models\Usuario;
use App\Services\UnifiedDataSyncService;
use App\Support\UnifiedValidation;
use Illuminate\Http\Request;

class CampaniaController extends Controller
{
    /** @return array<string, string> */
    private function reglasCampania(?int $ignoreId = null): array
    {
        return [
            'titulo'            => 'required|string|max:100',
            'descripcion'       => 'required|string',
            'fechainicio'       => 'required|date',
            'fechafin'          => 'nullable|date|after_or_equal:fechainicio',
            'metarecaudacion'   => 'required|numeric|min:0',
            'activa'            => 'nullable|boolean',
            'imagenurl'         => 'nullable|string|max:255',
            'fechacreacion'     => 'nullable|date',
            'usuarioidcreador'  => 'required|integer|'.UnifiedValidation::existsCoreUsuario(),
        ];
    }

    /** @return array<string, string> */
    private function mensajesCampania(): array
    {
        return [
            'usuarioidcreador.required' => 'Selecciona el responsable de la campaña.',
            'usuarioidcreador.exists'   => 'El responsable seleccionado no es válido.',
            'titulo.required'           => 'El título es obligatorio.',
            'metarecaudacion.required'  => 'Indica la meta de recaudación.',
        ];
    }

    public function index()
    {
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
        $usuarios = Usuario::orderBy('nombre')->orderBy('apellido')->get();

        return view('campanias.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->reglasCampania(), $this->mensajesCampania());

        $data = $request->only([
            'titulo', 'descripcion', 'fechainicio', 'fechafin', 'metarecaudacion',
            'imagenurl', 'fechacreacion', 'usuarioidcreador',
        ]);

        $data['activa'] = $request->boolean('activa', true);
        $data['montorecaudado'] = 0;

        $campania = Campania::create($data);
        app(UnifiedDataSyncService::class)->mirrorCampaniaToInventario($campania);

        return redirect()->route('campanias.index')->with('success', 'Campaña creada exitosamente.');
    }

    public function edit($id)
    {
        $campania = Campania::with('creador')->findOrFail($id);
        $usuarios = Usuario::orderBy('nombre')->orderBy('apellido')->get();

        return view('campanias.edit', compact('campania', 'usuarios'));
    }

    public function update(Request $request, $id)
    {
        $campania = Campania::findOrFail($id);

        $validated = $request->validate($this->reglasCampania($campania->campaniaid), $this->mensajesCampania());

        $data = $request->only([
            'titulo', 'descripcion', 'fechainicio', 'fechafin', 'metarecaudacion',
            'imagenurl', 'fechacreacion', 'usuarioidcreador',
        ]);

        $data['activa'] = $request->boolean('activa', false);

        if ($request->has('montorecaudado')) {
            $data['montorecaudado'] = $request->input('montorecaudado');
        }

        $campania->update($data);
        app(UnifiedDataSyncService::class)->mirrorCampaniaToInventario($campania->fresh());

        return redirect()->route('campanias.index')->with('success', 'Campaña actualizada.');
    }

    public function destroy($id)
    {
        $campania = Campania::findOrFail($id);

        if ($campania->donaciones()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar una campaña que ya tiene donaciones.');
        }

        $campania->delete();

        return redirect()->route('campanias.index')->with('success', 'Campaña eliminada.');
    }
}
