<?php

namespace App\Http\Controllers;

use App\Models\Campania;
use App\Models\Donacion;
use Illuminate\Http\Request;

class CampaniaCierreController extends Controller
{
    // ➊ Pantalla para seleccionar campaña
    public function seleccionarCampania()
    {
        // Si quieres, puedes filtrar solo activas
        $campanias = Campania::orderBy('activa', 'desc')
            ->orderBy('fechainicio', 'desc')
            ->get();

        return view('campanias.seleccionar_cierre', compact('campanias'));
    }

    // ➋ Mostrar resumen de una campaña seleccionada
    public function mostrarResumen(Request $request)
    {
        $request->validate([
            'campaniaid' => 'required|integer|exists:campanias,campaniaid',
        ]);

        $campania = Campania::findOrFail($request->campaniaid);

        // Traemos todas las donaciones de la campaña con sus relaciones
        $donaciones = Donacion::with([
                'usuario',
                'estado',
                'saldo',
                'asignacionesPivot.asignacion',
            ])
            ->where('campaniaid', $campania->campaniaid)
            ->get();

        // ======= TOTALES GENERALES (como ya tenías) =======
        $totalDonado = $donaciones->sum('monto');

        $totalAsignado = $donaciones->sum(function ($d) {
            return $d->asignacionesPivot
                ? $d->asignacionesPivot->sum('montoasignado')
                : 0;
        });

        $totalSaldo = $donaciones->sum(function ($d) {
            return optional($d->saldo)->saldodisponible ?? 0;
        });

        // ======= NUEVOS TOTALES PARA LAS TARJETAS =======
        // Solo donaciones MONETARIAS
        $donacionesMonetarias = $donaciones->where('tipodonacion', 'Monetaria');

        // Confirmadas = estados 2,3,4 (confirmada, asignada, utilizada)
        $montoConfirmadas = $donacionesMonetarias
            ->whereIn('estadoid', [2, 3, 4])
            ->sum('monto');

        // Pendientes = estado 1
        $montoPendientes = $donacionesMonetarias
            ->where('estadoid', 1)
            ->sum('monto');

        // Total monetario (todas las monetarias sin importar estado)
        $montoTotalMonetario = $donacionesMonetarias->sum('monto');

        $totales = [
            'total_donado'         => $totalDonado,
            'total_asignado'       => $totalAsignado,
            'total_saldo'          => $totalSaldo,

            // Para las tarjetas de arriba
            'monto_confirmadas'    => $montoConfirmadas,
            'monto_pendientes'     => $montoPendientes,
            'monto_total_monetario'=> $montoTotalMonetario,
        ];

        return view('campanias.resumen_cierre', compact('campania', 'donaciones', 'totales'));
    }

    // ➌ Acción para cerrar la campaña
    public function cerrarCampania(Request $request, $campaniaid)
    {
        $campania = Campania::findOrFail($campaniaid);

        if (!$campania->activa) {
            return back()->with('status', 'La campaña ya está cerrada.');
        }

        $campania->activa = false;

        if (!$campania->fechafin) {
            $campania->fechafin = now()->toDateString();
        }

        $campania->save();

        return redirect()
            ->route('campanias.cierreMostrar', ['campaniaid' => $campania->campaniaid])
            ->with('status', 'La campaña se cerró correctamente.');
    }
}
