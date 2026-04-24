<?php

namespace Modules\Inventario\Http\Controllers\Auth;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegistroSimpleController extends Controller
{
    /**
     * GET /api/registro/ci/{ci}
     * Devuelve datos básicos de un usuario por CI para autocompletar registros
     * usados por el API Gateway.
     */
    public function showByCi(Request $request, string $ci)
    {
        $clientSystem = $request->header('X-Client-System', 'unknown');

        Log::info('RegistroSimple lookup recibido', [
            'ci'            => $ci,
            'client_system' => $clientSystem,
            'ip'            => $request->ip(),
        ]);

        $user = Usuario::where('ci', $ci)->first();

        // NO devolver 404 si no existe
        if (!$user) {
            return response()->json([
                'success' => true,
                'system'  => 'donaciones',
                'ci'      => $ci,
                'found'   => false,
                'data'    => null,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'system'  => 'donaciones',
            'ci'      => $ci,
            'found'   => true,
            'data'    => [
                'ci'                 => $user->ci,
                'nombre'             => $user->nombres,
                'apellido'           => $user->apellidos,
                'telefono'           => $user->telefono,
                'correo'             => $user->correo,
            ],
        ], 200);
    }
}







