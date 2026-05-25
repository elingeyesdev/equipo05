<?php

namespace App\Http\Controllers;

use App\Models\{Donacion, Usuario, Campania, Estado, SaldosDonacion};
use App\Support\UnifiedValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DonacionController extends Controller
{
    /** @return array<string, string> */
    private function reglasDonacion(bool $incluirUsuario = true): array
    {
        $rules = [
            'campaniaid'    => 'required|integer|'.UnifiedValidation::existsTransparencia('campanias', 'campaniaid'),
            'monto'         => 'required|numeric|min:0.01',
            'tipodonacion'  => ['required', 'in:Monetaria,Especie,monetaria,especie'],
            'descripcion'   => 'nullable|string',
            'fechadonacion' => 'nullable|date',
            'estadoid'      => 'required|integer|'.UnifiedValidation::existsTransparencia('estados', 'estadoid'),
            'esanonima'     => 'nullable|boolean',
        ];

        if ($incluirUsuario) {
            $rules['usuarioid'] = 'required|integer|'.UnifiedValidation::existsCoreUsuario();
        } else {
            $rules['usuarioid'] = 'nullable|integer|'.UnifiedValidation::existsCoreUsuario();
        }

        return $rules;
    }

    /** @return array<string, string> */
    private function mensajesDonacion(): array
    {
        return [
            'campaniaid.required' => 'Selecciona una campaña.',
            'campaniaid.exists'   => 'La campaña seleccionada no es válida.',
            'estadoid.required'   => 'Selecciona un estado.',
            'estadoid.exists'     => 'El estado seleccionado no es válido.',
            'usuarioid.required'  => 'Selecciona el donante.',
            'usuarioid.exists'    => 'El donante seleccionado no es válido.',
            'monto.required'      => 'Indica el monto de la donación.',
            'monto.min'           => 'El monto debe ser mayor a cero.',
        ];
    }

/** LISTADO */
    public function index()
    {
        $donaciones = Donacion::with(['usuario','campania','estado','saldo'])
            ->orderByDesc('donacionid')
            ->get();

        return view('donaciones.index', compact('donaciones'));
    }

    /** FORM CREAR */
    public function create()
    {
        $usuarios  = Usuario::orderBy('nombre')->orderBy('apellido')->get();
        $campanias = Campania::where('activa', true)->orderByDesc('campaniaid')->get();
        $estados   = Estado::orderBy('estadoid')->get();

        return view('donaciones.create', compact('usuarios', 'campanias', 'estados'));
    }

    /** GUARDAR */
    public function store(Request $request)
    {
        $validated = $request->validate($this->reglasDonacion(), $this->mensajesDonacion());

        // Normalizar tipo
        $validated['tipodonacion'] = ucfirst(strtolower($validated['tipodonacion']));

        // Fecha
        if ($request->filled('fechadonacion')) {
            $validated['fechadonacion'] = \Carbon\Carbon::parse($request->input('fechadonacion'))
                ->format('Y-m-d H:i:s');
        }

        // Checkbox anónimo
        $validated['esanonima'] = $request->boolean('esanonima', false);

        DB::transaction(function () use ($validated) {
            $don = Donacion::create($validated);

            // Crear registro de saldo
            SaldosDonacion::firstOrCreate(
                ['donacionid' => $don->donacionid],
                [
                    'montooriginal'       => $don->monto,
                    'montoutilizado'      => 0,
                    'saldodisponible'     => $don->monto,
                    'ultimaactualizacion' => now(),
                ]
            );

            // Actualizar total recaudado en la campaña
            $sum = Donacion::where('campaniaid', $don->campaniaid)->sum('monto');
            Campania::where('campaniaid', $don->campaniaid)->update(['montorecaudado' => $sum]);
        });

        return redirect()->route('donaciones.index')->with('success','Donación creada exitosamente.');
    }

    /** FORM EDITAR */
    public function edit($id)
    {
        $donacion  = Donacion::findOrFail($id);
        
        // En EDITAR sí mandamos usuarios, por si un ADMINISTRADOR necesita corregir quién hizo la donación.
        $usuarios  = Usuario::orderBy('nombre')->get();
        $campanias = Campania::orderByDesc('campaniaid')->get();
        $estados   = Estado::orderBy('estadoid')->get();

        return view('donaciones.edit', compact('donacion','usuarios','campanias','estados'));
    }

    /** ACTUALIZAR */
    public function update(Request $request, $id)
    {
        $donacion = Donacion::findOrFail($id);

        $validated = $request->validate($this->reglasDonacion(false), $this->mensajesDonacion());

        $validated['tipodonacion'] = ucfirst(strtolower($validated['tipodonacion']));

        if ($request->filled('fechadonacion')) {
            $validated['fechadonacion'] = \Carbon\Carbon::parse($request->input('fechadonacion'))
                ->format('Y-m-d H:i:s');
        }

        $validated['esanonima'] = $request->boolean('esanonima', false);

        if (empty($validated['usuarioid'])) {
            $validated['usuarioid'] = null;
        }

        DB::transaction(function () use ($donacion, $validated) {
            $campaniaAnterior = $donacion->campaniaid;
            
            // Actualizamos la donación
            $donacion->update($validated);

            // Ajustar saldo (si el monto cambió)
            $saldo = SaldosDonacion::firstOrCreate(
                ['donacionid' => $donacion->donacionid],
                [
                    'montooriginal'       => $donacion->monto,
                    'montoutilizado'      => 0,
                    'saldodisponible'     => $donacion->monto,
                    'ultimaactualizacion' => now(),
                ]
            );

            if ($saldo->montooriginal != $donacion->monto) {
                $delta = $donacion->monto - $saldo->montooriginal;
                $saldo->montooriginal    = $donacion->monto;
                // Ajustamos el disponible con el delta, sin bajar de 0
                $saldo->saldodisponible  = max(0, $saldo->saldodisponible + $delta);
                $saldo->ultimaactualizacion = now();
                $saldo->save();
            }

            // Recalcular montos de campañas (Anterior y Nueva)
            if ($campaniaAnterior != $donacion->campaniaid) {
                $sumOld = Donacion::where('campaniaid', $campaniaAnterior)->sum('monto');
                Campania::where('campaniaid', $campaniaAnterior)->update(['montorecaudado' => $sumOld]);
            }
            $sumNew = Donacion::where('campaniaid', $donacion->campaniaid)->sum('monto');
            Campania::where('campaniaid', $donacion->campaniaid)->update(['montorecaudado' => $sumNew]);
        });

        return redirect()->route('donaciones.index')->with('success','Donación actualizada.');
    }

    /** ELIMINAR */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $don = Donacion::findOrFail($id);

            // Verificar si ya se usó dinero de esta donación
            $usos = \App\Models\DonacionesAsignacion::where('donacionid', $id)->count();
            if ($usos > 0) {
                // abort(422) lanzará una página de error, o puedes redirigir con error:
                throw new \Exception('No se puede eliminar: la donación ya fue asignada/utilizada.');
            }

            // Borrar saldo y donación
            SaldosDonacion::where('donacionid', $id)->delete();
            $don->delete();

            // Actualizar campaña
            $sum = Donacion::where('campaniaid', $don->campaniaid)->sum('monto');
            Campania::where('campaniaid', $don->campaniaid)->update(['montorecaudado' => $sum]);
        });

        return redirect()->route('donaciones.index')->with('success','Donación eliminada.');
    }

    public function reasignarForm($id)
    {
        $donacion = Donacion::findOrFail($id);
        $campanias = Campania::where('activa', true)->get();
        return view('donaciones.reasignar', compact('donacion', 'campanias'));
    }

    public function reasignar(Request $request, $id)
    {
        $donacion = Donacion::findOrFail($id);
        $data = $request->validate([
            'campaniaid' => 'required|integer|'.UnifiedValidation::existsTransparencia('campanias', 'campaniaid'),
        ], [
            'campaniaid.exists' => 'La campaña seleccionada no es válida.',
        ]);

        $campaniaNueva = Campania::findOrFail($data['campaniaid']);
        $campaniaAnterior = Campania::findOrFail($donacion->campaniaid);

        DB::transaction(function () use ($donacion, $campaniaAnterior, $campaniaNueva, $data) {
            $campaniaAnterior->montorecaudado -= $donacion->monto;
            $campaniaAnterior->save();

            $campaniaNueva->montorecaudado += $donacion->monto;
            $campaniaNueva->save();

            $donacion->campaniaid = $data['campaniaid'];
            $donacion->save();
        });

        return redirect()->route('donaciones.index')
            ->with('success', 'Donación reasignada correctamente.');
    }
}