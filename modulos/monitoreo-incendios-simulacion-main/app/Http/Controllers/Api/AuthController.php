<?php

namespace Modules\Incendios\Http\Controllers\Api;

use Modules\Incendios\Http\Controllers\Controller;
use Modules\Incendios\Models\User;
use App\Support\UnifiedValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $emailUnique = Rule::unique(UnifiedValidation::incendiosUsersTable(), 'email');

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', $emailUnique],
            'password' => 'required|string|min:8|confirmed',
            'telefono' => 'nullable|string|max:20',
        ];

        if (! \App\Support\UnifiedPostgres::enabled()) {
            $rules['cedula_identidad'] = ['nullable', 'string', 'max:50', Rule::unique('users', 'cedula_identidad')];
        }

        $validated = $request->validate($rules);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'telefono' => $validated['telefono'] ?? null,
            'cedula_identidad' => $validated['cedula_identidad'] ?? null,
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Login user and return token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->getAuthPassword())) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Cargar relaciones de roles
        $user->load(['administrador', 'voluntario']);

        // Eliminar tokens anteriores (opcional, para seguridad)
        $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión correcto.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'role' => $user->getRoleType(),
            'is_admin' => $user->isAdministrador(),
            'is_volunteer' => $user->isVoluntario(),
            'token' => $token,
        ]);
    }

    /**
     * Logout user (revoke token).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }
}
