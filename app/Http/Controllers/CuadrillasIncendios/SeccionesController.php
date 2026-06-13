<?php

namespace App\Http\Controllers\CuadrillasIncendios;

use App\Http\Controllers\Concerns\HandlesFusionModuloCrud;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SeccionesController extends Controller
{
    use HandlesFusionModuloCrud;

    protected function moduloConnection(): string
    {
        return 'cuadrillas';
    }

    protected function moduloRoutePrefix(): string
    {
        return 'cuadrillas';
    }

    protected function moduloCrudView(): string
    {
        return 'fusion.modulos.cuadrillas-crud-form';
    }

    protected function moduloWriteKey(): string
    {
        return 'cuadrillas';
    }

    protected function seccionesConfig(): array
    {
        return [
            'reportes' => ['titulo' => 'Reportes', 'tabla' => 'reporte', 'pk' => 'id_reporte'],
            'focos-calor' => ['titulo' => 'Mapa en Tiempo Real', 'tabla' => 'foco_calor', 'pk' => 'id_foco_calor'],
            'noticias' => ['titulo' => 'Noticias', 'tabla' => 'noticia', 'pk' => 'id_noticia'],
            'cursos' => ['titulo' => 'Cursos', 'tabla' => 'curso', 'pk' => 'id_curso'],
        ];
    }

    public function show(string $seccion): View
    {
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $pk = $config['pk'];
        $connection = $this->moduloConnection();

        if ($seccion === 'reportes') {
            $tiposIncidente = DB::connection($connection)->table('tipo_incidente')->orderBy('nombre')->get();
            $nivelesGravedad = DB::connection($connection)->table('nivel_gravedad')->orderBy('id_nivel_gravedad')->get();
            return view('fusion.modulos.cuadrillas-reporte-interno', compact('tiposIncidente', 'nivelesGravedad'));
        }

        if ($seccion === 'focos-calor') {
            $countEquiposDesplegados = DB::connection($connection)
                ->table('equipo')
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->count();

            $countReportes = DB::connection($connection)
                ->table('reporte')
                ->whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->count();

            $ultimoReporteFecha = DB::connection($connection)
                ->table('reporte')
                ->whereNotNull('fecha_hora')
                ->max('fecha_hora');

            $ultimoReporte = 'N/A';
            if ($ultimoReporteFecha) {
                $ultimoReporte = \Carbon\Carbon::parse($ultimoReporteFecha)->format('d/m/Y');
            }

            return view('fusion.modulos.cuadrillas-mapa-interno', compact('countEquiposDesplegados', 'countReportes', 'ultimoReporte'));
        }

        if ($seccion === 'noticias') {
            $noticias = DB::connection($connection)->table('noticia')
                ->orderByDesc('date')
                ->orderByDesc('id_noticia')
                ->paginate(9);

            foreach ($noticias as $noticia) {
                $noticia->date = $noticia->date 
                    ? \Carbon\Carbon::parse($noticia->date) 
                    : ($noticia->created_at ? \Carbon\Carbon::parse($noticia->created_at) : now());
            }

            return view('fusion.modulos.cuadrillas-noticias-interno', compact('noticias'));
        }

        if ($seccion === 'cursos') {
            $cursos = DB::connection($connection)->table('curso')
                ->orderByDesc('id_curso')
                ->get();

            foreach ($cursos as $curso) {
                $curso->cursos_asignados_count = DB::connection($connection)->table('inscrito')
                    ->where('id_curso', $curso->id_curso)
                    ->count();
                $curso->creado = $curso->created_at ? \Carbon\Carbon::parse($curso->created_at) : null;
            }

            return view('fusion.modulos.cuadrillas-cursos-interno', compact('cursos'));
        }

        abort(404);
    }

    public function scrapeNoticias()
    {
        try {
            \Artisan::call('scrape:incendios-news');
            return response()->json([
                'success' => true,
                'message' => 'Las noticias se han actualizado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar las noticias: ' . $e->getMessage()
            ], 500);
        }
    }
}
