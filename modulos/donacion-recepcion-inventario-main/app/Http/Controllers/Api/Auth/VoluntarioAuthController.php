<?php

namespace Modules\Inventario\Http\Controllers\Api\Auth;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VoluntarioAuthController extends Controller
{
    /**
     * Login de voluntario/usuario
     * 
     * Endpoint esperado por app móvil: POST /api/auth/login
     * Body: { "ci": "string", "contrasena": "string" }
     * Response: { "token": "string", "usuario": { "id": int, "nombres": "string" } }
     */
    public function login(Request $request)
    {
        $request->validate([
            'ci' => 'required|string',
            'contrasena' => 'required|string',
        ]);

        // Buscar usuario por CI
        $usuario = Usuario::where('ci', $request->ci)->first();

        if (!$usuario) {
            throw ValidationException::withMessages([
                'ci' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Verificar contraseña
        if (!Hash::check($request->contrasena, $usuario->contrasena)) {
            throw ValidationException::withMessages([
                'ci' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Generar token
        $token = $usuario->createToken('voluntario-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id_usuario,
                'nombres' => $usuario->nombres,
            ]
        ], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}







