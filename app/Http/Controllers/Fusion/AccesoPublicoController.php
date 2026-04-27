<?php

namespace App\Http\Controllers\Fusion;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AccesoPublicoController extends Controller
{
    public function cuadrillasMapa(): View
    {
        return $this->renderPublicTable(
            'Cuadrillas - Mapa en Tiempo Real',
            'Datos públicos de focos de calor y monitoreo',
            'cuadrillas',
            'foco_calor',
            'id_foco_calor'
        );
    }

    public function cuadrillasReporte(): View
    {
        return $this->renderPublicTable(
            'Cuadrillas - Reporte Público',
            'Canal de reporte ciudadano de incendios y emergencias',
            'cuadrillas',
            'reporte',
            'id_reporte'
        );
    }

    public function seguimientoInfo(): View
    {
        return $this->renderPublicTable(
            'Seguimiento de Voluntarios',
            'Vista pública informativa de actividad voluntaria',
            'seguimiento',
            'usuario',
            'id_usuario'
        );
    }

    private function renderPublicTable(
        string $titulo,
        string $subtitulo,
        string $connection,
        string $tabla,
        string $pk
    ): View {
        $columnas = [];
        $filas = collect();
        $total = 0;

        if (Schema::connection($connection)->hasTable($tabla)) {
            $columnas = Schema::connection($connection)->getColumnListing($tabla);
            $columnas = array_slice($columnas, 0, 8);

            $query = DB::connection($connection)->table($tabla);
            if (Schema::connection($connection)->hasColumn($tabla, 'created_at')) {
                $query->orderByDesc('created_at');
            } elseif (Schema::connection($connection)->hasColumn($tabla, $pk)) {
                $query->orderByDesc($pk);
            }

            $filas = $query->limit(20)->get($columnas);
            $total = DB::connection($connection)->table($tabla)->count();
        }

        return view('fusion.modulos.acceso-publico', compact(
            'titulo',
            'subtitulo',
            'columnas',
            'filas',
            'total'
        ));
    }
}
