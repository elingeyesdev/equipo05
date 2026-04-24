<?php

namespace Modules\Inventario\Http\Controllers\Api;

use Modules\Inventario\Http\Controllers\Controller;
use Modules\Inventario\Models\Usuario;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * GET /api/users/{userId}
     */
    public function show($id)
    {
        try {
            $usuario = Usuario::select('id_usuario', 'nombres', 'apellidos', 'ci', 'telefono', 'email', 'rol')
                ->findOrFail($id);

            return response()->json($usuario, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
    }

    public function index()
    {
        return Usuario::select('id_usuario', 'nombres', 'apellidos', 'ci', 'email', 'rol')->paginate(20);
    }

    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}







