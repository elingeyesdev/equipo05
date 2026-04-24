<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// CORRECCIÓN: Usamos el modelo de Spatie, NO App\Models\Role
use Spatie\Permission\Models\Role; 
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Mostrar lista de roles
     */
    public function index()
    {
        $roles = Role::all(); // Usa Spatie
        return view('roles.index', compact('roles'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Guardar nuevo rol
     */
    public function store(Request $request)
    {
        // Spatie usa 'name' en la BD, pero tu form manda 'nombre'
        $request->validate([
            'nombre' => 'required|unique:roles,name', 
        ]);

        Role::create([
            'name' => $request->nombre,
            'guard_name' => 'web',
            'descripcion' => $request->descripcion // Nuestro campo extra
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $role = Role::findById($id); // Helper de Spatie
        return view('roles.edit', compact('role'));
    }

    /**
     * Actualizar rol
     */
    public function update(Request $request, $id)
    {
        $role = Role::findById($id);

        $request->validate([
            'nombre' => 'required|unique:roles,name,' . $role->id,
        ]);

        $role->update([
            'name' => $request->nombre,
            'descripcion' => $request->descripcion
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Eliminar rol
     */
    public function destroy($id)
    {
        $role = Role::findById($id);
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}