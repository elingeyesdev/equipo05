<?php

namespace Modules\Rescate\Http\Controllers;

use App\Support\AccessControl;
use App\Support\RescateAccess;
use App\Support\UnifiedValidation;
use Modules\Rescate\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Rescate\Http\Requests\PersonRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Modules\Rescate\Services\User\UserTrackingService;

class PersonController extends Controller
{
    public function __construct()
    {
        // Debe estar autenticado
        $this->middleware('auth');
        // Solo administradores o encargados pueden ver personas
        // Solo administradores pueden crear, editar, actualizar o eliminar personas
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        RescateAccess::assertCanManagePeople();

        $query = Person::with('user.roles');

        // Filtro por nombre
        if ($request->filled('nombre')) {
            $query->where('nombre', 'like', '%' . $request->input('nombre') . '%');
        }

        // Filtro por email (a través de la relación user)
        if ($request->filled('email')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->input('email') . '%');
            });
        }

        // Filtro por CI
        if ($request->filled('ci')) {
            $query->where('ci', 'like', '%' . $request->input('ci') . '%');
        }

        // Filtro por es_cuidador
        if ($request->filled('es_cuidador')) {
            $esCuidador = $request->input('es_cuidador');
            if ($esCuidador === '1') {
                $query->where('es_cuidador', true);
            } elseif ($esCuidador === '0') {
                $query->where('es_cuidador', false);
            }
        }

        // Filtro por rol (usando roles de Spatie)
        if ($request->filled('rol')) {
            $rol = $request->input('rol');
            $query->whereHas('user.roles', function ($q) use ($rol) {
                $q->where('name', $rol);
            });
        }

        $people = $query->with(['rescuers', 'veterinarians'])->paginate()->withQueryString();
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('person.index', compact('people', 'roles'))
            ->with('i', ($request->input('page', 1) - 1) * $people->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        RescateAccess::assertCanManagePeople();

        $person = new Person();
        $centers = \Modules\Rescate\Models\Center::orderBy('nombre')->get(['id', 'nombre', 'latitud', 'longitud']);

        return view('person.create', compact('person', 'centers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonRequest $request): RedirectResponse
    {
        RescateAccess::assertCanManagePeople();

        $data = $request->validated();

        $names = UnifiedValidation::splitNombreCompleto($data['nombre']);

        // Crear usuario primero
        $user = \Modules\Rescate\Models\User::create([
            'email' => $data['email'],
            'password' => $data['password'],
            'nombre' => $names['nombre'],
            'apellido' => $names['apellido'],
        ]);
        
        // Preparar datos para la persona
        $personData = [
            'usuario_id' => $user->id,
            'nombre' => $data['nombre'],
            'ci' => $data['ci'],
            'telefono' => $data['telefono'] ?? null,
            'es_cuidador' => isset($data['es_cuidador']) && $data['es_cuidador'] == '1' ? true : false,
            'cuidador_center_id' => $data['cuidador_center_id'] ?? null,
            'cuidador_aprobado' => $data['cuidador_aprobado'] ?? null,
            'cuidador_motivo_revision' => $data['cuidador_motivo_revision'] ?? null,
        ];
        
        // Crear persona
        $person = Person::create($personData);
        
        // Asignar rol de ciudadano por defecto
        AccessControl::syncSingleRole(
            \App\Models\Usuario::findOrFail($user->getKey()),
            'Ciudadano'
        );
        
        // Lógica de asignación de rol cuidador
        // Solo se asigna el rol si:
        // 1. es_cuidador = true
        // 2. cuidador_motivo_revision NO es null (fue completado)
        // 3. cuidador_aprobado = true (si fue aprobado)
        $esCuidador = (bool) $person->es_cuidador;
        $aprobado = (bool) $person->cuidador_aprobado;
        $tieneMotivo = !empty(trim($person->cuidador_motivo_revision ?? ''));
        
        $shouldHaveRole = $esCuidador && $tieneMotivo && $aprobado;
        
        if ($shouldHaveRole) {
            // Asignar rol cuidador
            $cuidadorRole = Role::firstOrCreate(['name' => 'cuidador', 'guard_name' => 'web']);
            if (!$user->hasRole('cuidador')) {
                $user->assignRole($cuidadorRole);
            }
        }
        
        // Registrar tracking de creación
        try {
            app(UserTrackingService::class)->logUserRegistration($user, [
                'person' => [
                    'id' => $person->id,
                    'nombre' => $person->nombre,
                    'ci' => $person->ci,
                ],
                'created_by_admin' => true,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Error registrando tracking de usuario creado por admin: ' . $e->getMessage());
        }

        return Redirect::route('rescate.people.index')
            ->with('success', 'Persona y usuario creados correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $person = Person::with(['rescuers', 'veterinarians', 'user.roles', 'cuidadorCenter'])->findOrFail($id);
        
        // Verificar si la persona es admin
        $personIsAdmin = $person->user && AccessControl::userHasRole($person->user, 'Administrador');
        
        // Verificar si ya tiene registros de rescatista o veterinario
        $hasRescuer = $person->rescuers->isNotEmpty();
        $hasVeterinarian = $person->veterinarians->isNotEmpty();
        
        $isAdmin = Auth::check() && AccessControl::userHasRole(Auth::user(), 'Administrador');
        $isEncargado = false;
        $canApproveCuidador = AccessControl::userHasAnyRole(Auth::user(), ['Administrador', 'Veterinario']);
        
        // Verificar si hay solicitud de cuidador pendiente
        $cuidadorPendiente = (int)$person->es_cuidador === 1 && empty($person->cuidador_motivo_revision);

        // Obtener el tracking del usuario si tiene usuario asociado
        $userTracking = [];
        if ($person->user) {
            try {
                $trackingService = app(UserTrackingService::class);
                $userTracking = $trackingService->getUserHistory($person->user->id);
            } catch (\Exception $e) {
                \Log::warning('Error obteniendo tracking del usuario: ' . $e->getMessage());
            }
        }

        return view('person.show', compact('person', 'hasRescuer', 'hasVeterinarian', 'isAdmin', 'isEncargado', 'personIsAdmin', 'canApproveCuidador', 'cuidadorPendiente', 'userTracking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        RescateAccess::assertCanManagePeople();

        $person = Person::with('cuidadorCenter')->findOrFail($id);
        $centers = \Modules\Rescate\Models\Center::orderBy('nombre')->get(['id', 'nombre', 'latitud', 'longitud']);

        return view('person.edit', compact('person', 'centers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonRequest $request, Person $person): RedirectResponse
    {
        // Detectar si viene la acción de aprobar/rechazar cuidador desde el modal
        // Verificar ANTES de la validación de PersonRequest
        $hasAction = $request->has('action') && $request->filled('action');
        $hasMotivo = $request->filled('cuidador_motivo_revision');
        $isCuidadorAction = $hasAction && $hasMotivo;
        
        if ($isCuidadorAction) {
            // Validar específicamente para la acción de cuidador (bypass PersonRequest)
            $validated = $request->validate([
                'action' => 'required|in:approve,reject',
                'cuidador_motivo_revision' => 'required|string|min:3',
            ]);
            
            // Preparar datos para actualización
            $data = [
                'cuidador_aprobado' => $validated['action'] === 'approve' ? true : false,
                'cuidador_motivo_revision' => trim($validated['cuidador_motivo_revision']),
            ];
            
            // Asegurarse de que es_cuidador esté en true (es necesario para la aprobación)
            // Si se aprueba, es_cuidador debe ser true
            if ($validated['action'] === 'approve') {
                $data['es_cuidador'] = true;
            } else {
                // Si se rechaza, mantener es_cuidador como está pero limpiar aprobación
                $data['es_cuidador'] = $person->es_cuidador ?? false;
            }
        } else {
            // Validación normal usando PersonRequest
            $data = $request->validated();
            
            // Si es un encargado (y no admin), solo puede cambiar campos de aprobación de cuidador
            $user = Auth::user();
            if ($user && AccessControl::userHasRole($user, 'Veterinario') && ! AccessControl::userHasRole($user, 'Administrador')) {
                $allowedFields = ['cuidador_aprobado', 'cuidador_motivo_revision'];
                $data = array_intersect_key($data, array_flip($allowedFields));
            }
        }
        
        // Actualizar email del usuario si se proporciona
        if ($request->filled('email') && $person->user) {
            $person->user->update(['email' => $request->input('email')]);
        }
        
        // Remover email de $data para que no se intente actualizar en la tabla people
        unset($data['email']);
        
        // Actualizar la persona - usar fill y save para asegurar que se guarden todos los campos
        $person->fill($data);
        $saved = $person->save();
        
        // Si no se guardó correctamente, lanzar error
        if (!$saved) {
            return Redirect::back()
                ->withInput()
                ->with('error', 'Error al actualizar la información del cuidador.');
        }
        
        // Refrescar el modelo para obtener los valores actualizados
        $person->refresh();

        // Lógica de asignación de rol cuidador
        // Solo se asigna el rol si:
        // 1. es_cuidador = true
        // 2. cuidador_motivo_revision NO es null (fue completado por admin/encargado)
        // 3. cuidador_aprobado = true (si fue aprobado)
        if ($person->user) {
            // Usar comparación estricta con los valores cast del modelo
            $esCuidador = (bool) $person->es_cuidador;
            $aprobado = (bool) $person->cuidador_aprobado;
            $tieneMotivo = !empty(trim($person->cuidador_motivo_revision ?? ''));
            
            $shouldHaveRole = $esCuidador && $tieneMotivo && $aprobado;
            
            if ($shouldHaveRole) {
                AccessControl::syncSingleRole(
                    \App\Models\Usuario::findOrFail($person->user->getKey()),
                    'Cuidador'
                );
            } else {
                if ($person->user && AccessControl::userHasRole($person->user, 'Cuidador')) {
                    \App\Models\Usuario::findOrFail($person->user->getKey())->syncRoles([]);
                }
            }
        }

        // Si fue una acción de aprobación/rechazo, mostrar mensaje específico
        if ($isCuidadorAction) {
            $action = $request->input('action');
            $oldApproved = $person->getOriginal('cuidador_aprobado');
            
            // Registrar tracking de aprobación/rechazo de cuidador
            try {
                app(UserTrackingService::class)->logCaregiverApproval(
                    $person,
                    $action === 'approve',
                    $oldApproved,
                    $person->cuidador_motivo_revision
                );
            } catch (\Exception $e) {
                //
            }
            
            if ($action === 'approve') {
                $message = 'Solicitud de cuidador aprobada correctamente.';
                if ($person->user && AccessControl::userHasRole($person->user, 'Cuidador')) {
                    $message .= ' El rol de cuidador ha sido asignado.';
                }
            } else {
                $message = 'Solicitud de cuidador rechazada correctamente.';
            }
            return Redirect::route('rescate.people.show', $person->id)
                ->with('success', $message);
        }

        return Redirect::route('rescate.people.index')
            ->with('success', 'Persona actualizada correctamente');
    }

    public function destroy($id): RedirectResponse
    {
        RescateAccess::assertCanManagePeople();

        Person::findOrFail($id)->delete();

        return Redirect::route('rescate.people.index')
            ->with('success', 'Persona eliminada correctamente');
    }

    /**
     * Convert a person to encargado role
     */
    public function convertToEncargado($id): RedirectResponse
    {
        RescateAccess::assertCanManagePeople();

        return Redirect::back()
            ->with('warning', 'El rol encargado fue eliminado. Asigne un rol operativo específico (Rescatista, Veterinario, etc.) desde Administración de usuarios.');
    }

}
