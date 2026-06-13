<?php

namespace App\Http\Controllers\SeguimientoVoluntarios;

use App\Http\Controllers\Concerns\HandlesFusionModuloCrud;
use App\Http\Controllers\Controller;
use App\Support\FusionModuloAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\Request;

class SeccionesController extends Controller
{
    use HandlesFusionModuloCrud {
        crudCreate as traitCrudCreate;
        crudEdit as traitCrudEdit;
        crudStore as traitCrudStore;
        crudUpdate as traitCrudUpdate;
    }

    public function crudCreate(string $seccion): View
    {
        FusionModuloAccess::assertSeguimientoSection($seccion, true);
        if ($seccion === 'capacitaciones') {
            $secciones = $this->seccionesConfig();
            $config = $secciones[$seccion];
            return view('fusion.modulos.seguimiento-capacitaciones-form', [
                'seccion' => $seccion,
                'tituloSeccion' => $config['titulo'],
                'primaryKey' => $config['pk'],
                'registro' => null,
            ]);
        }
        if ($seccion === 'necesidades') {
            $secciones = $this->seccionesConfig();
            $config = $secciones[$seccion];
            return view('fusion.modulos.seguimiento-necesidades-form', [
                'seccion' => $seccion,
                'tituloSeccion' => $config['titulo'],
                'primaryKey' => $config['pk'],
                'registro' => null,
            ]);
        }
        if ($seccion === 'administradores') {
            $secciones = $this->seccionesConfig();
            $config = $secciones[$seccion];
            return view('fusion.modulos.seguimiento-administradores-form', [
                'seccion' => $seccion,
                'tituloSeccion' => $config['titulo'],
                'primaryKey' => $config['pk'],
                'registro' => null,
            ]);
        }
        return $this->traitCrudCreate($seccion);
    }

    public function crudEdit(string $seccion, int $id): View
    {
        FusionModuloAccess::assertSeguimientoSection($seccion, true);
        if ($seccion === 'capacitaciones') {
            $secciones = $this->seccionesConfig();
            $config = $secciones[$seccion];
            $connection = $this->moduloConnection();
            
            $registro = DB::connection($connection)
                ->table($config['tabla'])
                ->where($config['pk'], $id)
                ->first();
            abort_unless($registro, 404);

            return view('fusion.modulos.seguimiento-capacitaciones-form', [
                'seccion' => $seccion,
                'tituloSeccion' => $config['titulo'],
                'primaryKey' => $config['pk'],
                'registro' => $registro,
            ]);
        }
        if ($seccion === 'necesidades') {
            $secciones = $this->seccionesConfig();
            $config = $secciones[$seccion];
            $connection = $this->moduloConnection();
            
            $registro = DB::connection($connection)
                ->table($config['tabla'])
                ->where($config['pk'], $id)
                ->first();
            abort_unless($registro, 404);

            return view('fusion.modulos.seguimiento-necesidades-form', [
                'seccion' => $seccion,
                'tituloSeccion' => $config['titulo'],
                'primaryKey' => $config['pk'],
                'registro' => $registro,
            ]);
        }
        if ($seccion === 'administradores') {
            $secciones = $this->seccionesConfig();
            $config = $secciones[$seccion];
            $connection = $this->moduloConnection();
            
            $registro = DB::connection($connection)
                ->table($config['tabla'])
                ->where($config['pk'], $id)
                ->first();
            abort_unless($registro, 404);

            return view('fusion.modulos.seguimiento-administradores-form', [
                'seccion' => $seccion,
                'tituloSeccion' => $config['titulo'],
                'primaryKey' => $config['pk'],
                'registro' => $registro,
            ]);
        }
        return $this->traitCrudEdit($seccion, $id);
    }

    public function crudStore(Request $request, string $seccion)
    {
        FusionModuloAccess::assertSeguimientoSection($seccion, true);
        $secciones = $this->seccionesConfig();
        abort_unless(isset($secciones[$seccion]), 404);

        if ($seccion === 'evaluacion-pruebas') {
            $idVoluntario = $request->input('id_voluntario');
            $token = \Illuminate\Support\Str::random(40);
            $connection = $this->moduloConnection();
            
            DB::connection($connection)->table('evaluacion_tokens')->insert([
                'id_voluntario' => $idVoluntario,
                'token' => $token,
                'usado' => false,
                'fecha_expiracion' => now()->addDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('seguimiento.evaluacion-pruebas')->with('success', 'Token de invitación generado exitosamente.');
        }

        if ($seccion === 'capacitaciones') {
            $connection = $this->moduloConnection();
            $cursos = $request->input('cursos');
            if (is_array($cursos)) {
                $cursos = json_encode($cursos);
            }
            if (!$cursos || $cursos === 'null') {
                $cursos = '[]';
            }
            
            DB::connection($connection)->table('capacitacion')->insert([
                'nombre' => $request->input('nombre'),
                'descripcion' => $request->input('descripcion'),
                'cursos' => $cursos,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('seguimiento.capacitaciones')->with('success', 'Capacitación creada correctamente.');
        }

        if ($seccion === 'necesidades') {
            $connection = $this->moduloConnection();
            $tipo = $request->input('tipo');
            $descripcion = $request->input('descripcion');
            
            DB::connection($connection)->table('necesidad')->insert([
                'nombre' => $tipo,
                'tipo' => $tipo,
                'descripcion' => $descripcion,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('seguimiento.necesidades')->with('success', 'Necesidad creada correctamente.');
        }

        if ($seccion === 'administradores') {
            $connection = $this->moduloConnection();
            $nombre = $request->input('nombre');
            $apellido = $request->input('apellido');
            $email = $request->input('email');
            $ci = $request->input('ci');
            $ext = $request->input('ext');
            if ($ext) {
                $ci = $ci . ' ' . $ext;
            }
            $telefono = $request->input('telefono');

            DB::connection($connection)->table('usuario')->insert([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'ci' => $ci,
                'telefono' => $telefono,
                'administrador' => true,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->route('seguimiento.administradores')->with('success', 'Administrador creado correctamente.');
        }

        // Fallback to trait logic
        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $connection = $this->moduloConnection();
        $columns = $this->columnsForCrud($config['tabla'], $config['pk']);
        $data = $this->prepareStoreData($tabla, collect($request->only($columns))
            ->map(fn ($value) => $value === '' ? null : $value)
            ->toArray());

        if (Schema::connection($connection)->hasColumn($tabla, 'created_at')) {
            $data['created_at'] = now();
        }
        if (Schema::connection($connection)->hasColumn($tabla, 'updated_at')) {
            $data['updated_at'] = now();
        }

        DB::connection($connection)->table($tabla)->insert($data);

        return redirect()->route("seguimiento.{$seccion}")->with('success', 'Registro creado correctamente.');
    }

    public function crudUpdate(Request $request, string $seccion, int $id): \Illuminate\Http\RedirectResponse
    {
        FusionModuloAccess::assertSeguimientoSection($seccion, true);
        if ($seccion === 'capacitaciones') {
            $secciones = $this->seccionesConfig();
            $config = $secciones[$seccion];
            $connection = $this->moduloConnection();

            $cursos = $request->input('cursos');
            if (is_array($cursos)) {
                $cursos = json_encode($cursos);
            }
            if (!$cursos || $cursos === 'null') {
                $cursos = '[]';
            }

            DB::connection($connection)
                ->table($config['tabla'])
                ->where($config['pk'], $id)
                ->update([
                    'nombre' => $request->input('nombre'),
                    'descripcion' => $request->input('descripcion'),
                    'cursos' => $cursos,
                    'updated_at' => now(),
                ]);

            return redirect()->route('seguimiento.capacitaciones')->with('success', 'Capacitación actualizada correctamente.');
        }

        if ($seccion === 'necesidades') {
            $secciones = $this->seccionesConfig();
            $config = $secciones[$seccion];
            $connection = $this->moduloConnection();

            $tipo = $request->input('tipo');
            $descripcion = $request->input('descripcion');

            DB::connection($connection)
                ->table($config['tabla'])
                ->where($config['pk'], $id)
                ->update([
                    'nombre' => $tipo,
                    'tipo' => $tipo,
                    'descripcion' => $descripcion,
                    'updated_at' => now(),
                ]);

            return redirect()->route('seguimiento.necesidades')->with('success', 'Necesidad actualizada correctamente.');
        }

        if ($seccion === 'administradores') {
            $secciones = $this->seccionesConfig();
            $config = $secciones[$seccion];
            $connection = $this->moduloConnection();

            if ($request->has('toggle_active')) {
                // simple active status toggle
                $user = DB::connection($connection)->table($config['tabla'])->where($config['pk'], $id)->first();
                abort_unless($user, 404);
                
                DB::connection($connection)->table($config['tabla'])
                    ->where($config['pk'], $id)
                    ->update([
                        'activo' => !$user->activo,
                        'updated_at' => now(),
                    ]);
                
                $statusMsg = !$user->activo ? 'Administrador activado correctamente.' : 'Administrador desactivado correctamente.';
                return redirect()->route('seguimiento.administradores')->with('success', $statusMsg);
            }

            // full edit update
            $nombre = $request->input('nombre');
            $apellido = $request->input('apellido');
            $email = $request->input('email');
            $ci = $request->input('ci');
            $ext = $request->input('ext');
            if ($ext) {
                $ci = $ci . ' ' . $ext;
            }
            $telefono = $request->input('telefono');

            DB::connection($connection)->table($config['tabla'])
                ->where($config['pk'], $id)
                ->update([
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'email' => $email,
                    'ci' => $ci,
                    'telefono' => $telefono,
                    'updated_at' => now(),
                ]);

            return redirect()->route('seguimiento.administradores')->with('success', 'Administrador actualizado correctamente.');
        }

        return $this->traitCrudUpdate($request, $seccion, $id);
    }

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

    protected function moduloWriteKey(): string
    {
        return 'seguimiento';
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
        FusionModuloAccess::assertSeguimientoSection($seccion);
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
            
            if ($seccion === 'evaluacion') {
                return view('fusion.modulos.seguimiento-evaluacion', [
                    'seccion' => $seccion,
                    'tituloSeccion' => $config['titulo'],
                ]);
            }

            if ($seccion === 'evaluacion-pruebas') {
                $tokens = DB::connection($connection)->table('evaluacion_tokens')
                    ->leftJoin('usuario', 'evaluacion_tokens.id_voluntario', '=', 'usuario.id_usuario')
                    ->select('evaluacion_tokens.*', 'usuario.nombre as vol_nombre', 'usuario.apellido as vol_apellido', 'usuario.email as vol_email')
                    ->orderByDesc('evaluacion_tokens.created_at')
                    ->get();
                $voluntarios = DB::connection($connection)->table('usuario')->where('activo', true)->orderBy('nombre')->get();

                return view('fusion.modulos.seguimiento-evaluacion-pruebas', [
                    'seccion' => $seccion,
                    'tituloSeccion' => $config['titulo'],
                    'tokens' => $tokens,
                    'voluntarios' => $voluntarios,
                ]);
            }

            if ($seccion === 'capacitaciones') {
                $capacitaciones = DB::connection($connection)->table('capacitacion')
                    ->orderBy('nombre')
                    ->get();

                return view('fusion.modulos.seguimiento-capacitaciones', [
                    'seccion' => $seccion,
                    'tituloSeccion' => $config['titulo'],
                    'capacitaciones' => $capacitaciones,
                ]);
            }

            if ($seccion === 'necesidades') {
                $necesidades = DB::connection($connection)->table('necesidad')
                    ->orderBy('nombre')
                    ->get();

                return view('fusion.modulos.seguimiento-necesidades', [
                    'seccion' => $seccion,
                    'tituloSeccion' => $config['titulo'],
                    'necesidades' => $necesidades,
                ]);
            }

            if ($seccion === 'ayudas-solicitadas') {
                $solicitudes = DB::connection($connection)->table('solicitudes_ayuda')
                    ->leftJoin('usuario', 'solicitudes_ayuda.voluntario_id', '=', 'usuario.id_usuario')
                    ->select('solicitudes_ayuda.*', 'usuario.nombre as vol_nombre', 'usuario.apellido as vol_apellido')
                    ->orderByDesc('solicitudes_ayuda.created_at')
                    ->get()
                    ->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'voluntario_id' => $s->voluntario_id,
                            'voluntario' => trim(($s->vol_nombre ?? '') . ' ' . ($s->vol_apellido ?? '')),
                            'prioridad' => strtolower($s->prioridad ?? 'medio'),
                            'estado' => strtolower($s->estado ?? 'pendiente'),
                            'tipo' => $s->tipo ?? 'Otro',
                            'direccion' => $s->direccion ?? 'Ubicación reportada',
                            'detalle' => $s->descripcion ?? '',
                            'latitud' => (float) ($s->latitud ?? -17.806776),
                            'longitud' => (float) ($s->longitud ?? -63.15749),
                            'fecha' => $s->fecha ? date('d/m/Y H:i', strtotime($s->fecha)) : ($s->created_at ? date('d/m/Y H:i', strtotime($s->created_at)) : ''),
                        ];
                    });

                return view('fusion.modulos.seguimiento-ayudas-solicitadas', [
                    'seccion' => $seccion,
                    'tituloSeccion' => $config['titulo'],
                    'solicitudes' => $solicitudes,
                    'solicitudesJson' => json_encode($solicitudes),
                ]);
            }

            if ($seccion === 'administradores') {
                $query = DB::connection($connection)->table('usuario')->where('administrador', true);

                // Filter by name
                if (request()->filled('q')) {
                    $q = request('q');
                    $query->where(function($sub) use ($q) {
                        $sub->where('nombre', 'ILIKE', "%{$q}%")
                            ->orWhere('apellido', 'ILIKE', "%{$q}%");
                    });
                }
                
                // Filter by CI
                if (request()->filled('ci')) {
                    $query->where('ci', 'LIKE', '%' . request('ci') . '%');
                }

                // Filter by status
                if (request()->filled('estado')) {
                    $estado = request('estado');
                    if ($estado === 'activo') {
                        $query->where('activo', true);
                    } elseif ($estado === 'inactivo') {
                        $query->where('activo', false);
                    }
                }

                $admins = $query->orderBy('nombre')->get();

                return view('fusion.modulos.seguimiento-administradores', [
                    'seccion' => $seccion,
                    'tituloSeccion' => $config['titulo'],
                    'primaryKey' => $pk,
                    'administradores' => $admins,
                ]);
            }

            if ($seccion === 'voluntarios' || $seccion === 'voluntarios-inactivos') {
                $query = DB::connection($connection)->table($tabla);

                // Default to inactive if requested through 'voluntarios-inactivos'
                if ($seccion === 'voluntarios-inactivos') {
                    $query->where('activo', false);
                }

                // Filter by name/surname
                if (request()->filled('q')) {
                    $q = request('q');
                    $query->where(function($sub) use ($q) {
                        $sub->where('nombre', 'ILIKE', "%{$q}%")
                            ->orWhere('apellido', 'ILIKE', "%{$q}%");
                    });
                }

                // Filter by CI
                if (request()->filled('ci')) {
                    $query->where('ci', 'LIKE', '%' . request('ci') . '%');
                }

                // Filter by Blood Type
                if (request()->filled('tipo_sangre')) {
                    $query->where('tipo_sangre', request('tipo_sangre'));
                }

                // Filter by Availability (estado)
                if (request()->filled('estado')) {
                    $estado = request('estado');
                    if ($estado === 'activo') {
                        $query->where('activo', true);
                    } elseif ($estado === 'inactivo') {
                        $query->where('activo', false);
                    }
                }

                if (Schema::connection($connection)->hasColumn($tabla, 'created_at')) {
                    $query->orderByDesc('created_at');
                } else {
                    $query->orderByDesc($pk);
                }

                $filas = $query->get();
                $total = DB::connection($connection)->table($tabla)->count();

                return view('fusion.modulos.seguimiento-voluntarios-lista', [
                    'seccion' => $seccion,
                    'tituloSeccion' => $config['titulo'],
                    'nombreTabla' => $tabla,
                    'primaryKey' => $pk,
                    'voluntarios' => $filas,
                    'total' => $total,
                ]);
            }

            // Standard generic table listing for other sections
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

            $filas = $query->get($columnas);
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
