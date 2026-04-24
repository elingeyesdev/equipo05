<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\UsuarioRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $usuarios = Usuario::paginate();

        return view('inventario::usuario.index', compact('usuarios'))
            ->with('i', ($request->input('page', 1) - 1) * $usuarios->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $usuario = new Usuario();
        $roles = $this->getInventarioRoles();
        $usuarioRol = null;

        return view('inventario::usuario.create', compact('usuario', 'roles', 'usuarioRol'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UsuarioRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Hash de la contraseña
        if (isset($data['contrasena'])) {
            $data['contrasena'] = bcrypt($data['contrasena']);
        }
        
        $usuario = Usuario::create($data);
        
        if ($request->filled('rol')) {
            $this->assignInventarioRole((int) $usuario->id_usuario, (string) $request->rol);
        }

        return Redirect::route('inventario.usuario.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Usuario $usuario): View
    {
        return view('inventario::usuario.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Usuario $usuario): View
    {
        $roles = $this->getInventarioRoles();
        $usuarioRol = $this->getUsuarioRol($usuario);

        return view('inventario::usuario.edit', compact('usuario', 'roles', 'usuarioRol'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UsuarioRequest $request, Usuario $usuario): RedirectResponse
    {
        $data = $request->validated();
        
        // Hash de la contraseña solo si se proporciona una nueva
        if (isset($data['contrasena']) && !empty($data['contrasena'])) {
            $data['contrasena'] = bcrypt($data['contrasena']);
        } else {
            // No actualizar contraseña si está vacía
            unset($data['contrasena']);
        }
        
        $usuario->update($data);
        
        if ($request->filled('rol')) {
            $this->assignInventarioRole((int) $usuario->id_usuario, (string) $request->rol);
        }

        return Redirect::route('inventario.usuario.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
        DB::connection('inventario')
            ->table('model_has_roles')
            ->where('model_type', Usuario::class)
            ->where('model_id', $usuario->id_usuario)
            ->delete();

        $usuario->delete();

        return Redirect::route('inventario.usuario.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    private function getInventarioRoles()
    {
        $this->ensureInventarioRoles();

        return DB::connection('inventario')
            ->table('roles')
            ->select('id', 'name')
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();
    }

    private function ensureInventarioRoles(): void
    {
        $defaultRoles = ['Administrador', 'Almacenero', 'Reportes', 'Voluntario', 'Donante'];

        foreach ($defaultRoles as $roleName) {
            DB::connection('inventario')
                ->table('roles')
                ->updateOrInsert(
                    ['name' => $roleName, 'guard_name' => 'web'],
                    ['updated_at' => now(), 'created_at' => now()]
                );
        }
    }

    private function assignInventarioRole(int $usuarioId, string $roleName): void
    {
        $this->ensureInventarioRoles();

        $roleId = DB::connection('inventario')
            ->table('roles')
            ->where('name', $roleName)
            ->where('guard_name', 'web')
            ->value('id');

        if (!$roleId) {
            return;
        }

        DB::connection('inventario')
            ->table('model_has_roles')
            ->where('model_type', Usuario::class)
            ->where('model_id', $usuarioId)
            ->delete();

        DB::connection('inventario')
            ->table('model_has_roles')
            ->insert([
                'role_id' => $roleId,
                'model_type' => Usuario::class,
                'model_id' => $usuarioId,
            ]);
    }

    private function getUsuarioRol(Usuario $usuario): ?string
    {
        return DB::connection('inventario')
            ->table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', Usuario::class)
            ->where('model_has_roles.model_id', $usuario->id_usuario)
            ->value('roles.name');
    }
}







