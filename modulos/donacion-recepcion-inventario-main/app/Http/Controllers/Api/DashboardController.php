<?php

namespace Modules\Inventario\Http\Controllers\Api;

use App\Support\Database\YearMonthSql;
use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\Donacione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard/total-donaciones
     */
    public function getTotalDonaciones()
    {
        try {
            $totales = [
                'total' => Donacione::count(),
                'dinero' => Donacione::where('tipo', 'dinero')->count(),
                'especie' => Donacione::where('tipo', 'especie')->count(),
                'ropa' => Donacione::where('tipo', 'ropa')->count(),
            ];

            return response()->json($totales, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener totales'], 500);
        }
    }

    /**
     * GET /api/dashboard/donaciones-por-mes/{year}
     */
    public function getDonacionesPorMes($year)
    {
        try {
            $donacionesPorMes = Donacione::select(
                    DB::raw(YearMonthSql::monthSelect('fecha', 'inventario')),
                    DB::raw('COUNT(*) as total')
                )
                ->whereYear('fecha', $year)
                ->groupByRaw(YearMonthSql::monthGroupByRaw('fecha', 'inventario'))
                ->orderBy('mes')
                ->get();

            // Rellenar meses faltantes con 0
            $resultado = collect(range(1, 12))->map(function($mes) use ($donacionesPorMes) {
                $dato = $donacionesPorMes->firstWhere('mes', $mes);
                return [
                    'mes' => $mes,
                    'total' => $dato ? $dato->total : 0
                ];
            });

            return response()->json($resultado, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener donaciones por mes'], 500);
        }
    }

    public function index()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}







