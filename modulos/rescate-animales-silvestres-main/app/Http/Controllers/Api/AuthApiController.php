<?php
 
namespace Modules\Rescate\Http\Controllers\Api;
 
use Modules\Rescate\Http\Controllers\Controller;
use Modules\Rescate\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
 
class AuthApiController extends Controller
{
    /**
     * Login vía API y generación de token Sanctum (acción store del recurso).
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
 
        if (! Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }
 
        /** @var \Modules\Rescate\Models\User $user */
        $user = User::where('email', $request->email)->firstOrFail();
 
        // Crea el token para uso desde otro dispositivo (por ejemplo, app móvil)
        $token = $user->createToken('mobile_token')->plainTextToken;
 
        return response()->json([
            'message'      => 'Login exitoso',
            'token'        => $token,
            'user'         => $user->load('person'),
            'roles'        => $user->getRoleNames(),
            'permissions'  => $user->getAllPermissions()->pluck('name'),
            'highest_role' => $user->person?->highest_role,
        ]);
    }
}