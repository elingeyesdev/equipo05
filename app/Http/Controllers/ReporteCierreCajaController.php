<?php

namespace App\Http\Controllers;

use App\Models\Campania;
use App\Models\Donacion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\CierreCajaExport;
use Maatwebsite\Excel\Facades\Excel;

class ReporteCierreCajaController extends Controller
{
    /**
     * Lógica central de filtros para no repetir código.
     */
    private function aplicarFiltros($query, Request $request)
    {
        // 1. Campaña
        if ($request->filled('campaniaid')) {
            $query->where('campaniaid', $request->campaniaid);
        }

        // 2. Fechas
        if ($request->filled('desde')) {
            $query->whereDate('fechadonacion', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fechadonacion', '<=', $request->hasta);
        }

        // 3. Estado
        if ($request->filled('estadoid')) {
            $query->where('estadoid', $request->estadoid);
        }

        // 4. Tipo de Donación
        if ($request->filled('tipodonacion')) {
            $query->where('tipodonacion', $request->tipodonacion);
        }

        // 5. Buscador de Donante (Nombre o Apellido)
        if ($request->filled('donante')) {
            $term = $request->donante;
            $query->whereHas('usuario', function($q) use ($term) {
                $q->where('nombre', 'ILIKE', "%{$term}%") // Usa LIKE si es MySQL
                  ->orWhere('apellido', 'ILIKE', "%{$term}%");
            });
        }

        // 6. Rangos de Monto
        if ($request->filled('min_monto')) {
            $query->where('monto', '>=', $request->min_monto);
        }
        if ($request->filled('max_monto')) {
            $query->where('monto', '<=', $request->max_monto);
        }

        // 7. Privacidad (Anónimo)
        if ($request->filled('esanonima')) {
            $query->where('esanonima', $request->esanonima);
        }

        return $query;
    }

    public function index(Request $request)
    {
        // Consulta base con relaciones
        $query = Donacion::with(['usuario', 'campania', 'estado', 'asignacionesPivot.asignacion.detalles']);

        // APLICAMOS FILTROS
        $query = $this->aplicarFiltros($query, $request);

        $donaciones = $query->orderBy('fechadonacion', 'desc')->get();

        // Calcular totales basados en los resultados FILTRADOS
        $totalGeneral     = $donaciones->sum('monto');
        $totalConfirmadas = $donaciones->where('estadoid', 2)->sum('monto');
        $totalPendientes  = $donaciones->where('estadoid', 1)->sum('monto');

        $campanias = Campania::orderBy('titulo')->get();
        // Obtener tipos únicos para el select
        $tiposDonacion = Donacion::select('tipodonacion')->distinct()->pluck('tipodonacion');

        return view('reportes.cierre_caja', compact(
            'donaciones', 'campanias', 'totalGeneral', 
            'totalConfirmadas', 'totalPendientes', 'tiposDonacion'
        ));
    }

    public function exportarPDF(Request $request)
    {
        $query = Donacion::with([
            'usuario', 
            'campania', 
            'estado', 
            'asignacionesPivot.asignacion.detalles'
        ]);

        // Aplicamos los filtros (misma lógica que ya tienes)
        $query = $this->aplicarFiltros($query, $request);

        // OJO: Ordenamos primero por Campaña para que el reporte salga ordenado por grupos
        $donaciones = $query->orderBy('campaniaid', 'asc') // <--- IMPORTANTE
                            ->orderBy('fechadonacion', 'desc')
                            ->get();

        // Totales globales
        $totalGeneral     = $donaciones->sum('monto');
        $totalConfirmadas = $donaciones->where('estadoid', 2)->sum('monto');
        $totalPendientes  = $donaciones->where('estadoid', 1)->sum('monto');
        
        // Filtros para mostrar en el encabezado
        $filtrosAplicados = [
            'desde' => $request->desde,
            'hasta' => $request->hasta,
            'campania' => $request->campaniaid ? Campania::find($request->campaniaid)?->titulo : 'Todas',
            'donante' => $request->donante,
        ];

        $pdf = Pdf::loadView('reportes.cierre_caja_pdf', compact(
            'donaciones',
            'totalGeneral',
            'totalConfirmadas',
            'totalPendientes',
            'filtrosAplicados'
        ));

        $pdf->setPaper('letter', 'portrait');

        return $pdf->download('reporte_campanas_' . date('Y-m-d_His') . '.pdf');
    }

    public function exportarExcel(Request $request)
    {
        return Excel::download(
            new CierreCajaExport($request->all()), 
            'cierre_caja_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}