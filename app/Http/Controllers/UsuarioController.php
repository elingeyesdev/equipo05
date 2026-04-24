<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role; // <--- 1. Usamos el modelo de Spatie
use Illuminate\Support\Facades\Hash; // Para encriptar contraseña

class UsuarioController extends Controller
{
    public function index() {
        // Obtenemos usuarios con sus roles cargados
        $usuarios = Usuario::with('roles')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create() {
        // pluck para obtener solo nombre e id (optimización)
        $roles = Role::pluck('name', 'name')->all(); 
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request) {
        $request->validate([
            'email' => 'required|email|max:100|unique:usuarios,email',
            'contrasena' => 'required|string|max:255',
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'imagenurl' => 'nullable|string|max:255',
            'activo' => 'boolean', 
            'fecharegistro' => 'nullable|date',
            'roles' => 'array' 
        ]);

        // CORRECCIÓN: Quitamos 'roles' del input para que no intente guardarlo en la tabla usuarios
        $input = $request->except(['roles']); 
        
        // Encriptamos
        $input['contrasena'] = Hash::make($input['contrasena']); 

        // Creamos el usuario
        $usuario = Usuario::create($input);

        // Asignamos los roles (Spatie)
        if ($request->filled('roles')) {
            // Como usamos pluck('name', 'name'), aquí recibes los nombres (ej: ['Admin', 'Donante'])
            $usuario->assignRole($request->input('roles')); 
        }

        return redirect()->route('usuarios.index')->with('success','Usuario creado exitosamente.');
    }

    public function edit($id) {
        $usuario = Usuario::findOrFail($id);
        $roles = Role::pluck('name', 'name')->all();
        
        // Obtenemos los roles actuales del usuario para marcar los checkbox en la vista
        $userRoles = $usuario->roles->pluck('name','name')->all();

        return view('usuarios.edit', compact('usuario','roles', 'userRoles'));
    }

    public function update(Request $request, $id) {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'email' => 'required|email|max:100|unique:usuarios,email,' . $id . ',usuarioid',
            'contrasena' => 'nullable|string|max:255', // Nullable para no obligar a cambiarla siempre
            'nombre' => 'required|string|max:50',
            'apellido' => 'required|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'roles' => 'array'
        ]);

        $input = $request->except(['contrasena', 'roles']);

        // Solo actualizamos contraseña si el usuario escribió una nueva
        if ($request->filled('contrasena')) {
            $input['contrasena'] = Hash::make($request->contrasena);
        }

        $usuario->update($input);

        // 4. Sincronizar roles (Quita los viejos y pone los nuevos)
        if ($request->has('roles')) {
            $usuario->syncRoles($request->input('roles'));
        } else {
            // Si el array de roles viene vacío, le quitamos todos los roles (opcional)
            // $usuario->syncRoles([]); 
        }

        return redirect()->route('usuarios.index')->with('success','Usuario actualizado.');
    }

    public function destroy($id) {
        // En lugar de borrar físico, a veces es mejor desactivarlo, pero si prefieres borrar:
        Usuario::findOrFail($id)->delete();
        return redirect()->route('usuarios.index')->with('success','Usuario eliminado.');
    }
}