<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\UsuarioRequest;
use Illuminate\Support\Facades\Redirect;
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
        $roles = \Spatie\Permission\Models\Role::all();

        return view('inventario::usuario.create', compact('usuario', 'roles'));
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
        
        // Asignar rol con Spatie
        if ($request->has('rol')) {
            $usuario->assignRole($request->rol);
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
        $roles = \Spatie\Permission\Models\Role::all();
        return view('inventario::usuario.edit', compact('usuario', 'roles'));
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
        
        // Sincronizar rol con Spatie
        if ($request->has('rol')) {
            $usuario->syncRoles([$request->rol]);
        }

        return Redirect::route('inventario.usuario.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(Usuario $usuario): RedirectResponse
    {
        $usuario->delete();

        return Redirect::route('inventario.usuario.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}







