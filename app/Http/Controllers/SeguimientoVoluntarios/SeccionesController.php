<?php

namespace App\Http\Controllers\SeguimientoVoluntarios;

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
        return 'seguimiento';
    }

    protected function moduloRoutePrefix(): string
    {
        return 'seguimiento';
    }

    protected function moduloCrudView(): string
    {
        return 'fusion.modulos.seguimiento-crud-form';
    }

    protected function seccionesConfig(): array
    {
        return [
            'voluntarios' => ['titulo' => 'Voluntarios', 'tabla' => 'usuario', 'pk' => 'id_usuario'],
            'voluntarios-inactivos' => ['titulo' => 'Voluntarios Inactivos', 'tabla' => 'usuario', 'pk' => 'id_usuario', 'inactivos' => true],
            'evaluacion' => ['titulo' => 'Evaluacion', 'tabla' => 'evaluacion', 'pk' => 'id_evaluacion'],
            'evaluacion-pruebas' => ['titulo' => 'Evaluacion Voluntarios', 'tabla' => 'evaluacion_tokens', 'pk' => 'id'],
            'capacitaciones' => ['titulo' => 'Capacitaciones', 'tabla' => 'capacitacion', 'pk' => 'id_capacitacion'],
            'necesidades' => ['titulo' => 'Necesidades', 'tabla' => 'necesidad', 'pk' => 'id_necesidad'],
            'ayudas-solicitadas' => ['titulo' => 'Ayudas Solicitadas', 'tabla' => 'solicitudes_ayuda', 'pk' => 'id'],
            'administradores' => ['titulo' => 'Administradores', 'tabla' => 'usuario', 'pk' => 'id_usuario', 'admins' => true],
            'universidades' => ['titulo' => 'Universidades', 'tabla' => 'universidad', 'pk' => 'id_universidad'],
            'chat-consulta' => ['titulo' => 'Chat de Voluntarios', 'tabla' => 'chat_mensajes', 'pk' => 'id'],
            'helpdesk' => ['titulo' => 'Centro de Soporte', 'tabla' => 'consultas', 'pk' => 'id'],
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

        $columnas = [];
        $filas = collect();
        $total = 0;

        if (Schema::connection($connection)->hasTable($tabla)) {
            $columnas = Schema::connection($connection)->getColumnListing($tabla);
            $columnas = array_slice($columnas, 0, 10);

            $query = DB::connection($connection)->table($tabla);

            if (($config['inactivos'] ?? false) && Schema::connection($connection)->hasColumn($tabla, 'activo')) {
                $query->where('activo', 0);
            }

            if (($config['admins'] ?? false) && Schema::connection($connection)->hasColumn($tabla, 'administrador')) {
                $query->where('administrador', 1);
            }

            if (Schema::connection($connection)->hasColumn($tabla, 'created_at')) {
                $query->orderByDesc('created_at');
            } elseif (Schema::connection($connection)->hasColumn($tabla, $pk)) {
                $query->orderByDesc($pk);
            }

            $filas = $query->limit(20)->get($columnas);
            $total = DB::connection($connection)->table($tabla)->count();
        }

        return view('fusion.modulos.seguimiento-seccion', [
            'seccion' => $seccion,
            'tituloSeccion' => $config['titulo'],
            'nombreTabla' => $tabla,
            'primaryKey' => $pk,
            'columnas' => $columnas,
            'filas' => $filas,
            'total' => $total,
        ]);
    }
}
