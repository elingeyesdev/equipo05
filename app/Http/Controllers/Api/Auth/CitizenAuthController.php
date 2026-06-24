<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class CitizenAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($data['email']));
        $user = Usuario::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user || ! Hash::check($data['password'], (string) $user->contrasena)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        if ($user->activo === false) {
            throw ValidationException::withMessages([
                'email' => ['Esta cuenta está desactivada.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('alas-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión correcto.',
            'token' => $token,
            'usuario' => $this->serializeUser($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var Usuario $user */
        $user = $request->user();

        return response()->json([
            'usuario' => $this->serializeUser($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }

    public function misSolicitudes(Request $request): JsonResponse
    {
        /** @var Usuario $user */
        $user = $request->user();
        $ci = $this->normalizeCi($user->cedula_identidad);

        if ($ci === null) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'No hay CI asociada a tu cuenta.',
            ]);
        }

        if (! Schema::connection('logistica')->hasTable('solicitud')) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $conn = DB::connection('logistica');
        $rows = $conn->table('solicitud')
            ->join('solicitante', 'solicitud.id_solicitante', '=', 'solicitante.id_solicitante')
            ->join('destino', 'solicitud.id_destino', '=', 'destino.id_destino')
            ->where(function ($query) use ($ci, $user) {
                $query->where('solicitante.ci', $ci);
                if (! empty($user->telefono)) {
                    $tel = preg_replace('/\D+/', '', (string) $user->telefono);
                    if ($tel !== '') {
                        $query->orWhere('solicitante.telefono', 'like', '%'.$tel.'%');
                    }
                }
            })
            ->orderByDesc('solicitud.created_at')
            ->limit(50)
            ->get([
                'solicitud.codigo_seguimiento',
                'solicitud.estado',
                'solicitud.tipo_emergencia',
                'solicitud.cantidad_personas',
                'solicitud.fecha_solicitud',
                'solicitud.aprobada',
                'destino.comunidad',
                'destino.provincia',
            ]);

        return response()->json([
            'success' => true,
            'data' => $rows->map(fn ($row) => [
                'codigo_seguimiento' => $row->codigo_seguimiento,
                'estado' => $row->estado,
                'tipo_emergencia' => $row->tipo_emergencia,
                'cantidad_personas' => (int) $row->cantidad_personas,
                'fecha_solicitud' => $row->fecha_solicitud,
                'aprobada' => (bool) $row->aprobada,
                'comunidad' => $row->comunidad,
                'provincia' => $row->provincia,
            ])->values(),
        ]);
    }

    public function misHallazgos(Request $request): JsonResponse
    {
        /** @var Usuario $user */
        $user = $request->user();

        if (! Schema::connection('rescate')->hasTable('reports')) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $conn = DB::connection('rescate');
        $personIds = collect();

        if (Schema::connection('rescate')->hasTable('people')) {
            $personIds = $conn->table('people')
                ->where('usuario_id', $user->getKey())
                ->pluck('id');
        }

        $query = $conn->table('reports')->orderByDesc('created_at')->limit(50);

        if ($personIds->isNotEmpty()) {
            $query->whereIn('persona_id', $personIds);
        } else {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'Aún no tienes hallazgos vinculados a tu cuenta.',
            ]);
        }

        $rows = $query->get([
            'id',
            'observaciones',
            'direccion',
            'latitud',
            'longitud',
            'aprobado',
            'urgencia',
            'created_at',
        ]);

        return response()->json([
            'success' => true,
            'data' => $rows->map(fn ($row) => [
                'id' => (int) $row->id,
                'observaciones' => $row->observaciones,
                'direccion' => $row->direccion,
                'latitud' => $row->latitud !== null ? (float) $row->latitud : null,
                'longitud' => $row->longitud !== null ? (float) $row->longitud : null,
                'aprobado' => (bool) $row->aprobado,
                'urgencia' => $row->urgencia,
                'created_at' => $row->created_at,
            ])->values(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeUser(Usuario $user): array
    {
        return [
            'id' => (int) $user->getKey(),
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'email' => $user->email,
            'telefono' => $user->telefono,
            'cedula_identidad' => $user->cedula_identidad,
            'rol' => $user->primaryRoleName(),
        ];
    }

    private function normalizeCi(?string $ci): ?string
    {
        if ($ci === null || $ci === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $ci);

        return $digits !== '' ? $digits : $ci;
    }
}
