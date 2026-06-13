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
        if ($this->isVoluntarioSection($seccion)) {
            return $this->voluntarioFormView($seccion, null);
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
        if ($this->isVoluntarioSection($seccion)) {
            $secciones = $this->seccionesConfig();
            $config = $secciones[$seccion];
            $registro = DB::connection($this->moduloConnection())
                ->table($config['tabla'])
                ->where($config['pk'], $id)
                ->where('administrador', false)
                ->first();
            abort_unless($registro, 404);

            return $this->voluntarioFormView($seccion, $registro);
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

        if ($this->isVoluntarioSection($seccion)) {
            $connection = $this->moduloConnection();
            $data = $this->validatedVoluntarioData($request);
            $data['created_at'] = now();
            $data['updated_at'] = now();

            DB::connection($connection)->table('usuario')->insert($data);

            return redirect()
                ->route($seccion === 'voluntarios-inactivos' ? 'seguimiento.voluntarios-inactivos' : 'seguimiento.voluntarios')
                ->with('success', 'Voluntario creado correctamente.');
        }

        // Fallback to trait logic
        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $connection = $this->moduloConnection();
        $columns = $this->normalizeCrudColumns($seccion, $this->columnsForCrud($config['tabla'], $config['pk']));
        $data = $this->prepareStoreData($tabla, collect($request->only($columns))
            ->map(fn ($value) => $value === '' ? null : $value)
            ->toArray());

        if (in_array($seccion, ['voluntarios', 'voluntarios-inactivos'], true)) {
            $data['administrador'] = false;
        }

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

        if ($this->isVoluntarioSection($seccion)) {
            $secciones = $this->seccionesConfig();
            $config = $secciones[$seccion];
            $connection = $this->moduloConnection();

            $exists = DB::connection($connection)
                ->table($config['tabla'])
                ->where($config['pk'], $id)
                ->where('administrador', false)
                ->exists();
            abort_unless($exists, 404);

            $data = $this->validatedVoluntarioData($request);
            unset($data['administrador']);
            $data['updated_at'] = now();

            DB::connection($connection)
                ->table($config['tabla'])
                ->where($config['pk'], $id)
                ->update($data);

            return redirect()
                ->route($seccion === 'voluntarios-inactivos' ? 'seguimiento.voluntarios-inactivos' : 'seguimiento.voluntarios')
                ->with('success', 'Voluntario actualizado correctamente.');
        }

        $secciones = $this->seccionesConfig();
        $config = $secciones[$seccion];
        $tabla = $config['tabla'];
        $connection = $this->moduloConnection();
        $columns = $this->normalizeCrudColumns($seccion, $this->columnsForCrud($config['tabla'], $config['pk']));
        $data = $this->prepareStoreData($tabla, collect($request->only($columns))
            ->map(fn ($value) => $value === '' ? null : $value)
            ->toArray());

        if (in_array($seccion, ['voluntarios', 'voluntarios-inactivos'], true)) {
            unset($data['administrador']);
        }

        if (Schema::connection($connection)->hasColumn($tabla, 'updated_at')) {
            $data['updated_at'] = now();
        }

        DB::connection($connection)
            ->table($tabla)
            ->where($config['pk'], $id)
            ->update($data);

        return redirect()->route("seguimiento.{$seccion}")->with('success', 'Registro actualizado correctamente.');
    }

    protected function normalizeCrudColumns(string $seccion, array $columns): array
    {
        if (in_array($seccion, ['voluntarios', 'voluntarios-inactivos'], true)) {
            $columns = array_values(array_filter($columns, fn ($column) => $column !== 'administrador'));
            $preferred = ['nombre', 'apellido', 'email', 'ci', 'tipo_sangre', 'telefono', 'activo'];
            $ordered = array_values(array_filter($preferred, fn ($column) => in_array($column, $columns, true)));
            $rest = array_values(array_diff($columns, $ordered));

            return array_merge($ordered, $rest);
        }

        return $columns;
    }

    protected function prepareStoreData(string $tabla, array $data): array
    {
        $schema = Schema::connection($this->moduloConnection());

        foreach ($data as $column => $value) {
            try {
                $type = $schema->getColumnType($tabla, $column);
            } catch (\Throwable) {
                continue;
            }

            if (in_array($type, ['boolean', 'bool'], true)) {
                $data[$column] = $this->castBooleanValue($value);
            }
        }

        return $data;
    }

    private function castBooleanValue(mixed $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value !== 0;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'on', 'yes', 'si', 'sí', 'activo'], true);
    }

    private function isVoluntarioSection(string $seccion): bool
    {
        return in_array($seccion, ['voluntarios', 'voluntarios-inactivos'], true);
    }

    private function tiposSangreValidos(): array
    {
        return ['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'];
    }

    private function voluntarioFormView(string $seccion, ?object $registro): View
    {
        $config = $this->seccionesConfig()[$seccion];

        return view('fusion.modulos.seguimiento-voluntarios-form', [
            'seccion' => $seccion,
            'tituloSeccion' => $config['titulo'],
            'primaryKey' => $config['pk'],
            'registro' => $registro,
            'tiposSangre' => $this->tiposSangreValidos(),
        ]);
    }

    private function validatedVoluntarioData(Request $request): array
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'min:2', 'max:150'],
            'apellido' => ['required', 'string', 'min:2', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'ci' => ['required', 'string', 'regex:/^[0-9]{6,8}$/'],
            'ext' => ['nullable', 'string', 'max:3'],
            'telefono' => ['nullable', 'string', 'regex:/^[0-9]{7,8}$/'],
            'tipo_sangre' => ['nullable', 'string', 'in:'.implode(',', $this->tiposSangreValidos())],
            'activo' => ['nullable', 'in:0,1'],
        ], [
            'ci.regex' => 'La cédula debe tener entre 6 y 8 dígitos numéricos.',
            'telefono.regex' => 'El teléfono debe tener 7 u 8 dígitos numéricos.',
            'tipo_sangre.in' => 'Seleccione un tipo de sangre válido (O+, A-, etc.).',
        ]);

        $ci = $validated['ci'];
        if (! empty($validated['ext'])) {
            $ci .= ' '.$validated['ext'];
        }

        $data = [
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'email' => $validated['email'],
            'ci' => $ci,
            'telefono' => $validated['telefono'] ?? null,
            'tipo_sangre' => $validated['tipo_sangre'] ?? null,
            'activo' => $request->input('activo', '1') === '1',
            'administrador' => false,
        ];

        if (! Schema::connection($this->moduloConnection())->hasColumn('usuario', 'ci')) {
            unset($data['ci']);
        }
        if (! Schema::connection($this->moduloConnection())->hasColumn('usuario', 'tipo_sangre')) {
            unset($data['tipo_sangre']);
        }
        if (! Schema::connection($this->moduloConnection())->hasColumn('usuario', 'telefono')) {
            unset($data['telefono']);
        }

        return $data;
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
