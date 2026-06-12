<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Support\AccessControl;
use App\Support\UnifiedValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index() {
        // Obtenemos usuarios con sus roles cargados
        $usuarios = Usuario::with('roles')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create() {
        $roles = array_combine(AccessControl::FINAL_ROLES, AccessControl::FINAL_ROLES);

        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request) {
        $request->validate([
            'email' => ['required', 'email', 'max:100', Rule::unique(UnifiedValidation::coreUsuariosTable(), 'email')],
            'contrasena' => 'required|string|max:255',
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'imagenurl' => 'nullable|string|max:255',
            'activo' => 'boolean', 
            'fecharegistro' => 'nullable|date',
            'roles' => ['nullable', 'array', 'max:1'],
            'roles.*' => ['string', Rule::in(AccessControl::FINAL_ROLES)],
        ]);

        // CORRECCIÓN: Quitamos 'roles' del input para que no intente guardarlo en la tabla usuarios
        $input = $request->except(['roles']);
        
        // Encriptamos
        $input['contrasena'] = Hash::make($input['contrasena']); 

        // Creamos el usuario
        $usuario = Usuario::create($input);

        // Asignamos los roles (Spatie)
        if ($request->filled('roles')) {
            AccessControl::syncSingleRole($usuario, (string) $request->input('roles.0'));
        }

        return redirect()->route('usuarios.index')->with('success','Usuario creado exitosamente.');
    }

    public function edit($id) {
        $usuario = Usuario::findOrFail($id);
        $roles = array_combine(AccessControl::FINAL_ROLES, AccessControl::FINAL_ROLES);
        $userRoles = $usuario->roles->pluck('name', 'name')->all();

        return view('usuarios.edit', compact('usuario','roles', 'userRoles'));
    }

    public function update(Request $request, $id) {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'email' => ['required', 'email', 'max:100', Rule::unique(UnifiedValidation::coreUsuariosTable(), 'email')->ignore($usuario->usuarioid, UnifiedValidation::coreUsuariosKey())],
            'contrasena' => 'nullable|string|max:255',
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'roles' => ['nullable', 'array', 'max:1'],
            'roles.*' => ['string', Rule::in(AccessControl::FINAL_ROLES)],
        ]);

        $input = $request->except(['contrasena', 'roles']);

        // Solo actualizamos contraseña si el usuario escribió una nueva
        if ($request->filled('contrasena')) {
            $input['contrasena'] = Hash::make($request->contrasena);
        }

        $usuario->update($input);

        // 4. Sincronizar roles (Quita los viejos y pone los nuevos)
        if ($request->has('roles')) {
            AccessControl::syncSingleRole($usuario, (string) ($request->input('roles.0') ?? ''));
        }

        return redirect()->route('usuarios.index')->with('success','Usuario actualizado.');
    }

    public function destroy($id) {
        $usuario = Usuario::findOrFail($id);
        /** @var Usuario|null $authUser */
        $authUser = Auth::user();

        if ($authUser && (int) $authUser->usuarioid === (int) $usuario->usuarioid) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        if ($usuario->hasRole('Administrador')) {
            $adminsActivos = Usuario::role('Administrador')->where('activo', true)->count();
            if ($adminsActivos <= 1 && $usuario->activo) {
                return redirect()->route('usuarios.index')->with('error', 'No se puede eliminar al último administrador activo.');
            }
        }

        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success','Usuario eliminado.');
    }
}