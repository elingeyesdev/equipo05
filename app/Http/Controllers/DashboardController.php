<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Campania;
use App\Models\Donacion;
use App\Models\Usuario;
use App\Models\Mensaje;
use App\Models\Asignacion;
use App\Support\AccessControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\Usuario $user */
        $user = Auth::user();

        if (! $user->hasRole('Administrador')) {
            return redirect(AccessControl::redirectPathFor($user));
        }

        return $this->dashboardAdmin();
    }

    /**
     * Carga la lógica pesada solo para el Administrador
     */
    private function dashboardAdmin()
    {
        // =========================
        //   CAMPAÑAS
        // =========================
        $totalCampanias   = Campania::count();
        $campaniasActivas = Campania::where('activa', 1)->count();

        // =========================
        //   DONACIONES MONETARIAS
        // =========================
        // 2 = Confirmada, 3 = Asignada, 4 = Utilizada
        $estadosRecaudado = [2, 3, 4];

        $totalDonaciones = Donacion::where('tipodonacion', 'Monetaria')
            ->whereIn('estadoid', $estadosRecaudado)
            ->count();

        $montoDonadoTotal = Donacion::where('tipodonacion', 'Monetaria')
            ->whereIn('estadoid', $estadosRecaudado)
            ->sum('monto');

        $donantesUnicos = Donacion::where('tipodonacion', 'Monetaria')
            ->whereIn('estadoid', $estadosRecaudado)
            ->whereNotNull('usuarioid')
            ->distinct('usuarioid')
            ->count('usuarioid');

        // =========================
        //   USUARIOS & MENSAJES
        // =========================
        $totalUsuarios    = Usuario::count();
        $mensajesTotales  = Mensaje::count();
        $mensajesNoLeidos = Mensaje::where('leido', false)->count();

        $ultimosMensajes = Mensaje::with('usuario')
            ->orderByDesc('mensajeid')
            ->take(5)
            ->get();

        // =========================
        //   ASIGNACIONES
        // =========================
        $asignacionesTotal = Asignacion::count();
        $asignacionesMonto = Asignacion::sum('monto');

        // =========================
        //   GRÁFICO: DONACIONES POR MES (compatible con sqlite/pgsql/mysql)
        // =========================
        $driver = Donacion::query()->getConnection()->getDriverName();
        $mesExpr = match ($driver) {
            'sqlite' => "strftime('%Y-%m', fechadonacion)",
            'mysql' => "DATE_FORMAT(fechadonacion, '%Y-%m')",
            default => "TO_CHAR(fechadonacion, 'YYYY-MM')",
        };

        $donacionesPorMes = Donacion::select(
                DB::raw("$mesExpr as mes"),
                DB::raw("SUM(monto) as total")
            )
            ->where('tipodonacion', 'Monetaria')
            ->whereIn('estadoid', $estadosRecaudado)
            ->whereNotNull('fechadonacion')
            ->groupBy(DB::raw($mesExpr))
            ->orderBy('mes')
            ->limit(6)
            ->get();

        $chartMeses  = $donacionesPorMes->pluck('mes');
        $chartMontos = $donacionesPorMes->pluck('total');

        // =========================
        //   TOP CAMPAÑAS
        // =========================
        $topCampanias = Campania::withSum(['donaciones as recaudado_monetario' => function ($q) use ($estadosRecaudado) {
                $q->where('tipodonacion', 'Monetaria')
                  ->whereIn('estadoid', $estadosRecaudado);
            }], 'monto')
            ->orderByDesc('recaudado_monetario')
            ->take(4)
            ->get();

        // =========================
        //   ÚLTIMOS REGISTROS
        // =========================
        $ultimasDonaciones = Donacion::with(['usuario', 'campania', 'estado'])
            ->orderByDesc('donacionid')
            ->take(5)
            ->get();

        $ultimosUsuarios = Usuario::orderByDesc('usuarioid')
            ->take(8)
            ->get();

        return view('dashboard.index', compact(
            'totalCampanias', 'campaniasActivas', 'totalDonaciones',
            'montoDonadoTotal', 'donantesUnicos', 'totalUsuarios',
            'mensajesTotales', 'mensajesNoLeidos', 'asignacionesTotal',
            'asignacionesMonto', 'chartMeses', 'chartMontos',
            'topCampanias', 'ultimasDonaciones', 'ultimosUsuarios',
            'ultimosMensajes'
        ));
    }
}
