<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class AdministradorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $administradores = Administrador::with('user')->paginate(10);

        return view('administrador.index', compact('administradores'))
            ->with('i', ($request->input('page', 1) - 1) * $administradores->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $administrador = new Administrador();
        return view('administrador.create', compact('administrador'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'departamento' => 'required|string|max:255',
            'nivel_acceso' => 'required|integer|min:1|max:5',
            'activo' => 'boolean',
        ]);

        // Crear usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Crear administrador
        Administrador::create([
            'user_id' => $user->id,
            'departamento' => $request->departamento,
            'nivel_acceso' => $request->nivel_acceso,
            'activo' => $request->has('activo') ? true : false,
        ]);

        // Assign administrador role using Spatie
        $user->assignRole('administrador');

        return Redirect::route('administradores.index')
            ->with('success', 'Administrador creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $administrador = Administrador::with('user', 'simulaciones')->findOrFail($id);
        return view('administrador.show', compact('administrador'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $administrador = Administrador::with('user')->findOrFail($id);
        return view('administrador.edit', compact('administrador'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $administrador = Administrador::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $administrador->user_id,
            'password' => 'nullable|string|min:8|confirmed',
            'departamento' => 'required|string|max:255',
            'nivel_acceso' => 'required|integer|min:1|max:5',
            'activo' => 'boolean',
        ]);

        // Actualizar usuario
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $administrador->user->update($userData);

        // Actualizar administrador
        $administrador->update([
            'departamento' => $request->departamento,
            'nivel_acceso' => $request->nivel_acceso,
            'activo' => $request->has('activo') ? true : false,
        ]);

        return Redirect::route('administradores.index')
            ->with('success', 'Administrador actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $administrador = Administrador::findOrFail($id);
        $user = $administrador->user;
        
        $administrador->delete();
        $user->delete();

        return Redirect::route('administradores.index')
            ->with('success', 'Administrador eliminado exitosamente.');
    }
}
