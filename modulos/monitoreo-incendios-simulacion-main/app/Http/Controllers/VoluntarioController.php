<?php

namespace Modules\Incendios\Http\Controllers;

use Modules\Incendios\Models\Voluntario;
use Modules\Incendios\Models\User;
use App\Support\UnifiedValidation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class VoluntarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $voluntarios = Voluntario::with('user')->paginate(10);

        return view('voluntario.index', compact('voluntarios'))
            ->with('i', ($request->input('page', 1) - 1) * $voluntarios->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $voluntario = new Voluntario();
        return view('voluntario.create', compact('voluntario'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $emailUnique = Rule::unique(UnifiedValidation::incendiosUsersTable(), 'email');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', $emailUnique],
            'password' => 'required|string|min:8|confirmed',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'zona' => 'required|string|max:255',
            'notas' => 'nullable|string',
        ]);

        // Crear usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        // Crear voluntario
        Voluntario::create([
            'user_id' => $user->id,
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'zona' => $request->zona,
            'notas' => $request->notas,
        ]);

        // Assign voluntario role using Spatie
        $user->assignRole('voluntario');

        return Redirect::route('incendios.voluntarios.index')
            ->with('success', 'Voluntario creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $voluntario = Voluntario::with('user')->findOrFail($id);
        return view('voluntario.show', compact('voluntario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $voluntario = Voluntario::with('user')->findOrFail($id);
        return view('voluntario.edit', compact('voluntario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $voluntario = Voluntario::findOrFail($id);
        
        $emailUnique = Rule::unique(UnifiedValidation::incendiosUsersTable(), 'email')
            ->ignore($voluntario->user_id, UnifiedValidation::incendiosUsersKey());

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', $emailUnique],
            'password' => 'nullable|string|min:8|confirmed',
            'direccion' => 'required|string|max:255',
            'ciudad' => 'required|string|max:255',
            'zona' => 'required|string|max:255',
            'notas' => 'nullable|string',
        ]);

        // Actualizar usuario
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        if ($request->filled('password')) {
            $userData['password'] = $request->password;
        }
        
        $voluntario->user->update($userData);

        // Actualizar voluntario
        $voluntario->update([
            'direccion' => $request->direccion,
            'ciudad' => $request->ciudad,
            'zona' => $request->zona,
            'notas' => $request->notas,
        ]);

        return Redirect::route('incendios.voluntarios.index')
            ->with('success', 'Voluntario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): RedirectResponse
    {
        $voluntario = Voluntario::findOrFail($id);
        $user = $voluntario->user;

        $voluntario->delete();
        $user?->delete();

        return Redirect::route('incendios.voluntarios.index')
            ->with('success', 'Voluntario eliminado exitosamente.');
    }
}

