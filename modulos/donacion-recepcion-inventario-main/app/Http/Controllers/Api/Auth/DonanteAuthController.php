<?php

namespace Modules\Inventario\Http\Controllers\Api\Auth;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\Donante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class DonanteAuthController extends Controller
{
    /**
     * Login de donante
     * 
     * Endpoint esperado por app móvil: POST /api/donante-auth/login
     * Body: { "usuario": "string", "contrasena_hash": "string" }
     * Response: { "token": "string", "donante": { "id": int, "nombres": "string" } }
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'usuario' => 'required|string',
                'contrasena_hash' => 'required|string',
            ]);

            // Buscar donante por email
            $donante = Donante::where('email', $request->usuario)->first();

            if (!$donante) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las credenciales proporcionadas son incorrectas.',
                    'errors' => [
                        'usuario' => ['Usuario no encontrado']
                    ]
                ], 401);
            }

            // Verificar contraseña
            if (!Hash::check($request->contrasena_hash, $donante->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las credenciales proporcionadas son incorrectas.',
                    'errors' => [
                        'contrasena_hash' => ['Contraseña incorrecta']
                    ]
                ], 401);
            }

            // Generar token
            $token = $donante->createToken('donante-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'donante' => [
                    'id' => $donante->id_donante,
                    'nombres' => $donante->nombre,
                    'cambiar_password' => $donante->cambiar_password,
                ]
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'nueva_password' => 'required|string|min:6',
            ]);

            $donante = $request->user();
            
            $donante->update([
                'password' => Hash::make($request->nueva_password),
                'cambiar_password' => false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente',
                'cambiar_password' => false,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}







