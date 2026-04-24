<?php

namespace App\Http\Controllers;

use App\Models\{
    Asignacion,
    Campania,
    Usuario,
    DetallesAsignacion,
    DonacionesAsignacion,
    SaldosDonacion,
    Donacion
};  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AsignacionController extends Controller
{
    /** LISTADO */
    public function index()
    {
        $asignaciones = Asignacion::with(['campania','usuario'])
            ->withCount([
                'detalles as detalles_count',
                'donacionesPivot as donaciones_count',
            ])
            ->orderByDesc('asignacionid')
            ->paginate(10);

        // Para filtros o modales rápidos
        $campanias = Campania::orderByDesc('campaniaid')->get();
        $usuarios  = Usuario::orderByDesc('usuarioid')->get();

        return view('asignaciones.index', compact('asignaciones','campanias','usuarios'));
    }

    /** SHOW */
    public function show($id)
    {
        $asignacion = Asignacion::with('campania')->findOrFail($id);

        $detalles   = DetallesAsignacion::where('asignacionid',$id)
                        ->orderByDesc('detalleid')
                        ->get();

        $donacionesAsignadas = DonacionesAsignacion::where('asignacionid',$id)
                                ->orderByDesc('donacionasignacionid')
                                ->get();

        return view('asignaciones.detalles', compact(
            'asignacion',
            'detalles',
            'donacionesAsignadas'
        ));
    }

    /** FORM CREAR */
    public function create()
    {
        $campanias = Campania::orderByDesc('campaniaid')->get();
        // NO enviamos usuarios, el responsable es el logueado
        return view('asignaciones.create', compact('campanias'));
    }

    /** GUARDAR */
    public function store(Request $request)
    {
        $data = $request->validate([
            'campaniaid'      => 'required|integer|exists:campanias,campaniaid',
            'descripcion'     => 'required|string|max:255',
            'monto'           => 'required|numeric|min:0',
            'fechaasignacion' => 'nullable|date',
            'imagenurl'       => 'nullable|string|max:255',
            // 'usuarioid' eliminado de validación
            'comprobante'     => 'nullable|string|max:255',
        ]);

        // ASIGNACIÓN AUTOMÁTICA
        $data['usuarioid'] = Auth::id();

        if ($request->filled('fechaasignacion')) {
            $data['fechaasignacion'] = \Carbon\Carbon::parse(
                $request->input('fechaasignacion')
            )->format('Y-m-d H:i:s');
        }

        Asignacion::create($data);

        return redirect()->route('asignaciones.index')->with('success', 'Asignación creada.');
    }

    /** FORM EDITAR */
    public function edit($id)
    {
        $asignacion = Asignacion::findOrFail($id);
        $campanias  = Campania::orderByDesc('campaniaid')->get();
        // En editar enviamos usuarios por si el admin cambia el responsable
        $usuarios   = Usuario::orderByDesc('usuarioid')->get();

        return view('asignaciones.edit', compact('asignacion','campanias','usuarios'));
    }

    /** ACTUALIZAR */
    public function update(Request $request, $id)
    {
        $asignacion = Asignacion::findOrFail($id);

        $data = $request->validate([
            'campaniaid'      => 'required|integer|exists:campanias,campaniaid',
            'descripcion'     => 'required|string|max:255',
            'monto'           => 'required|numeric|min:0',
            'fechaasignacion' => 'nullable|date',
            'imagenurl'       => 'nullable|string|max:255',
            'usuarioid'       => 'required|integer|exists:usuarios,usuarioid',
            'comprobante'     => 'nullable|string|max:255',
        ]);

        if ($request->filled('fechaasignacion')) {
            $data['fechaasignacion'] = \Carbon\Carbon::parse(
                $request->input('fechaasignacion')
            )->format('Y-m-d H:i:s');
        }

        $asignacion->update($data);

        return redirect()->route('asignaciones.index')->with('success', 'Asignación actualizada.');
    }

    /** ELIMINAR */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $rows = DonacionesAsignacion::where('asignacionid',$id)->get();

                foreach ($rows as $r) {
                    $saldo = SaldosDonacion::where('donacionid',$r->donacionid)->first();
                    if ($saldo) {
                        $saldo->saldodisponible      += $r->montoasignado;
                        $saldo->montoutilizado       -= $r->montoasignado;
                        $saldo->ultimaactualizacion   = now();
                        $saldo->save();

                        $don = Donacion::find($r->donacionid);
                        if ($don) {
                            if ($saldo->saldodisponible >= $saldo->montooriginal) {
                                $don->estadoid = 2; // Confirmada
                            } elseif ($saldo->saldodisponible <= 0) {
                                $don->estadoid = 4; // Utilizada
                            } else {
                                $don->estadoid = 3; // Asignada parcial
                            }
                            $don->save();
                        }
                    }
                }

                DonacionesAsignacion::where('asignacionid',$id)->delete();
                DetallesAsignacion::where('asignacionid',$id)->delete();
                Asignacion::where('asignacionid',$id)->delete();
            });

            return redirect()->route('asignaciones.index')->with('success','Asignación eliminada.');
        } catch (\Throwable $e) {
            return back()->withErrors('Error al eliminar: '.$e->getMessage());
        }
    }

    public function detalles($id)
    {
        return $this->show($id);
    }

    public function guardarDetalle(Request $request, $id)
    {
        $asignacion = Asignacion::findOrFail($id);

        $data = $request->validate([
            'concepto'       => 'required|string|max:100',
            'cantidad'       => 'required|integer|min:1',
            'preciounitario' => 'required|numeric|min:0',
            'imagen'         => 'nullable|image|max:4096',
        ]);

        $detalle = new DetallesAsignacion();
        $detalle->asignacionid   = $asignacion->asignacionid;
        $detalle->concepto       = $data['concepto'];
        $detalle->cantidad       = $data['cantidad'];
        $detalle->preciounitario = $data['preciounitario'];

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('detalles', 'public');
            $detalle->imagenurl = Storage::url($path);
        }

        DB::transaction(function () use ($detalle, $asignacion) {
            $detalle->save();
            $total = DetallesAsignacion::where('asignacionid', $asignacion->asignacionid)
                ->select(DB::raw('COALESCE(SUM(cantidad*preciounitario),0) as total'))
                ->value('total');
            $asignacion->monto = $total;
            $asignacion->save();
        });

        return back()->with('success','Ítem agregado. Total actualizado.');
    }

    public function asignar($id)
    {
        $asignacion = Asignacion::with('campania')->findOrFail($id);
        $yaAsignado = DonacionesAsignacion::where('asignacionid',$id)->sum('montoasignado');
        $faltante   = max(0, ($asignacion->monto ?? 0) - $yaAsignado);

        $todasDonaciones = Donacion::get();
        foreach ($todasDonaciones as $don) {
            SaldosDonacion::firstOrCreate(
                ['donacionid' => $don->donacionid],
                [
                    'montooriginal'       => $don->monto,
                    'montoutilizado'      => 0,
                    'saldodisponible'     => $don->monto,
                    'ultimaactualizacion' => now(),
                ]
            );
        }

        $saldos = SaldosDonacion::query()
            ->join('donaciones','donaciones.donacionid','=','saldosdonaciones.donacionid')
            ->where('saldosdonaciones.saldodisponible','>',0)
            ->where('donaciones.campaniaid', $asignacion->campaniaid)
            ->select('saldosdonaciones.*')
            ->get();

        return view('asignaciones.asignar', compact(
            'asignacion',
            'yaAsignado',
            'faltante',
            'saldos'
        ));
    }

    public function guardarAsignacion(Request $request, $id)
    {
        $asignacion = Asignacion::findOrFail($id);

        $data = $request->validate([
            'donacionid'    => 'required|exists:donaciones,donacionid',
            'montoasignado' => 'required|numeric|min:0.01',
        ]);

        $saldo = SaldosDonacion::where('donacionid',$data['donacionid'])->first();
        if (!$saldo) {
            return back()->withErrors('No se encontró el saldo de la donación.');
        }

        $yaAsignado = DonacionesAsignacion::where('asignacionid',$asignacion->asignacionid)
            ->sum('montoasignado');

        $disponibleAsignacion = max(0, ($asignacion->monto ?? 0) - $yaAsignado);

        if ($disponibleAsignacion <= 0) {
            return back()->withErrors('Esta asignación ya fue cubierta al 100%.');
        }

        $maxPermitido = min($disponibleAsignacion, $saldo->saldodisponible);
        if ($data['montoasignado'] > $maxPermitido) {
            return back()->withErrors(
                'Monto inválido. Máximo permitido: Bs '.number_format($maxPermitido,2,'.',',')
            );
        }

        DB::transaction(function () use ($data, $asignacion, $saldo) {
            DonacionesAsignacion::create([
                'donacionid'      => $data['donacionid'],
                'asignacionid'    => $asignacion->asignacionid,
                'montoasignado'   => $data['montoasignado'],
                'fechaasignacion' => now(),
            ]);

            $saldo->saldodisponible      -= $data['montoasignado'];
            $saldo->montoutilizado       += $data['montoasignado'];
            $saldo->ultimaactualizacion   = now();
            $saldo->save();

            $don = Donacion::find($data['donacionid']);
            if ($don) {
                if ($saldo->saldodisponible <= 0) {
                    $don->estadoid = 4;
                } else {
                    $don->estadoid = 3;
                }
                $don->save();
            }
        });

        return redirect()->route('asignaciones.index')->with('success','Donación asignada correctamente.');
    }
}