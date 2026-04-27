<?php

namespace App\Http\Controllers\CuadrillasIncendios;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SeccionesController extends Controller
{
    public function show(string $seccion): View
    {
        $secciones = [
            'reportes' => ['titulo' => 'Reportes', 'tabla' => 'reporte', 'pk' => 'id_reporte'],
            'reportes-incendio' => ['titulo' => 'Reportes de Incendio', 'tabla' => 'reporte_incendio', 'pk' => 'id_reporte_incendio'],
            'focos-calor' => ['titulo' => 'Mapa en Tiempo Real', 'tabla' => 'foco_calor', 'pk' => 'id_foco_calor'],
            'equipos' => ['titulo' => 'Equipos', 'tabla' => 'equipo', 'pk' => 'id_equipo'],
            'recursos' => ['titulo' => 'Recursos', 'tabla' => 'recurso', 'pk' => 'id_recurso'],
            'noticias' => ['titulo' => 'Noticias', 'tabla' => 'noticia', 'pk' => 'id_noticia'],
            'cursos' => ['titulo' => 'Cursos', 'tabla' => 'curso', 'pk' => 'id_curso'],
            'inscritos' => ['titulo' => 'Inscritos', 'tabla' => 'inscrito', 'pk' => 'id_inscrito'],
            'comunarios' => ['titulo' => 'Comunarios de Apoyo', 'tabla' => 'comunario', 'pk' => 'id_comunario'],
            'usuarios' => ['titulo' => 'Usuarios', 'tabla' => 'usuario', 'pk' => 'id_usuario'],
            'roles' => ['titulo' => 'Roles', 'tabla' => 'role', 'pk' => 'id'],
            'generos' => ['titulo' => 'Generos', 'tabla' => 'genero', 'pk' => 'id_genero'],
            'tipos-sangre' => ['titulo' => 'Tipos de Sangre', 'tabla' => 'tipo_sangre', 'pk' => 'id_tipo_sangre'],
            'niveles-entrenamiento' => ['titulo' => 'Niveles de Entrenamiento', 'tabla' => 'nivel_entrenamiento', 'pk' => 'id_nivel_entrenamiento'],
            'niveles-gravedad' => ['titulo' => 'Niveles de Gravedad', 'tabla' => 'nivel_gravedad', 'pk' => 'id_nivel_gravedad'],
            'tipos-incidente' => ['titulo' => 'Tipos de Incidente', 'tabla' => 'tipo_incidente', 'pk' => 'id_tipo_incidente'],
            'tipos-recurso' => ['titulo' => 'Tipos de Recurso', 'tabla' => 'tipo_recurso', 'pk' => 'id_tipo_recurso'],
            'condiciones-climaticas' => ['titulo' => 'Condiciones Climaticas', 'tabla' => 'condicion_climatica', 'pk' => 'id_condicion_climatica'],
            'estados-sistema' => ['titulo' => 'Estados del Sistema', 'tabla' => 'estado_sistema', 'pk' => 'id_estado_sistema'],
            'kardex' => ['titulo' => 'Mi Kardex', 'tabla' => 'kardex', 'pk' => 'id_kardex'],
            'helpdesk' => ['titulo' => 'Centro de Soporte', 'tabla' => 'consultas', 'pk' => 'id'],
        ];

        abort_unless(isset($secciones[$seccion]), 404);

        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $connection = 'cuadrillas';
        $columnas = [];
        $filas = collect();
        $total = 0;

        if (Schema::connection($connection)->hasTable($tabla)) {
            $columnas = Schema::connection($connection)->getColumnListing($tabla);
            $columnas = array_slice($columnas, 0, 10);

            $query = DB::connection($connection)->table($tabla);
            if (Schema::connection($connection)->hasColumn($tabla, 'created_at')) {
                $query->orderByDesc('created_at');
            } elseif (Schema::connection($connection)->hasColumn($tabla, $config['pk'])) {
                $query->orderByDesc($config['pk']);
            }

            $filas = $query->limit(20)->get($columnas);
            $total = DB::connection($connection)->table($tabla)->count();
        }

        return view('fusion.modulos.cuadrillas-seccion', [
            'tituloSeccion' => $config['titulo'],
            'nombreTabla' => $tabla,
            'columnas' => $columnas,
            'filas' => $filas,
            'total' => $total,
        ]);
    }
}
