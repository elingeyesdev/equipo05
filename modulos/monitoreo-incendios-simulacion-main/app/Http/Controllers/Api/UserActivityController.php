<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Biomasa;
use App\Models\Simulacione;
use App\Models\Prediction;
use App\Models\User;

class UserActivityController extends Controller
{
    /**
     * Obtener todas las actividades de un usuario por su CI
     */
    public function getActivitiesByCi($ci)
    {
        // Verificar que el usuario existe
        $user = User::where('cedula_identidad', $ci)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado con el CI proporcionado'
            ], 404);
        }

        $biomasas = Biomasa::withTrashed()->where('ci_usuario', $ci)
            ->with(['tipoBiomasa', 'user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($biomasa) {
                return [
                    'id' => $biomasa->id,
                    'tipo' => 'biomasa',
                    'fecha_creacion' => $biomasa->created_at,
                    'fecha_eliminacion' => $biomasa->deleted_at ?? null,
                    'tipo_biomasa' => $biomasa->tipoBiomasa->tipo_biomasa ?? 'N/A',
                    'area_m2' => $biomasa->area_m2,
                    'estado' => $biomasa->estado,
                    'descripcion' => $biomasa->descripcion,
                    'ubicacion' => $biomasa->ubicacion,
                    'status' => $biomasa->trashed() ? 'deleted' : 'active',
                ];
            });

        $simulaciones = Simulacione::withTrashed()->where('ci_usuario', $ci)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sim) {
                return [
                    'id' => $sim->id,
                    'tipo' => 'simulacion',
                    'fecha_creacion' => $sim->created_at,
                    'fecha_eliminacion' => $sim->deleted_at ?? null,
                    'nombre' => $sim->nombre,
                    'fecha_simulacion' => $sim->fecha,
                    'duracion' => $sim->duracion,
                    'estado' => $sim->estado,
                    'focos_activos' => $sim->focos_activos,
                    'status' => $sim->trashed() ? 'deleted' : 'active',
                ];
            });

        $predictions = Prediction::withTrashed()->where('ci_usuario', $ci)
            ->with('focoIncendio')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pred) {
                return [
                    'id' => $pred->id,
                    'tipo' => 'prediccion',
                    'fecha_creacion' => $pred->created_at,
                    'fecha_eliminacion' => $pred->deleted_at ?? null,
                    'predicted_at' => $pred->predicted_at,
                    'foco_id' => $pred->foco_incendio_id,
                    'meta' => $pred->meta,
                    'status' => $pred->trashed() ? 'deleted' : 'active',
                ];
            });

        // Combinar y ordenar todas las actividades
        $allActivities = $biomasas->concat($simulaciones)->concat($predictions)
            ->sortByDesc('fecha_creacion')
            ->values();

        return response()->json([
            'success' => true,
            'ci' => $ci,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'data' => [
                'biomasas' => $biomasas->values(),
                'simulaciones' => $simulaciones->values(),
                'predictions' => $predictions->values(),
                'todas' => $allActivities,
            ],
            'totales' => [
                'biomasas' => $biomasas->count(),
                'simulaciones' => $simulaciones->count(),
                'predictions' => $predictions->count(),
                'total' => $allActivities->count(),
            ]
        ]);
    }

    /**
     * Obtener estadísticas de actividad por CI
     */
    public function getStatsByCi($ci)
    {
        // Verificar que el usuario existe
        $user = User::where('cedula_identidad', $ci)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado con el CI proporcionado'
            ], 404);
        }

        $biomasasPendientes = Biomasa::where('ci_usuario', $ci)
            ->where('estado', 'pendiente')->count();
        $biomasasAprobadas = Biomasa::where('ci_usuario', $ci)
            ->where('estado', 'aprobada')->count();
        $biomasasRechazadas = Biomasa::where('ci_usuario', $ci)
            ->where('estado', 'rechazada')->count();

        $simulacionesTotal = Simulacione::where('ci_usuario', $ci)->count();
        $predictionsTotal = Prediction::where('ci_usuario', $ci)->count();

        // Actividad reciente (últimos 30 días)
        $biomasasRecientes = Biomasa::where('ci_usuario', $ci)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $simulacionesRecientes = Simulacione::where('ci_usuario', $ci)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        $predictionsRecientes = Prediction::where('ci_usuario', $ci)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return response()->json([
            'success' => true,
            'ci' => $ci,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'stats' => [
                'biomasas' => [
                    'pendientes' => $biomasasPendientes,
                    'aprobadas' => $biomasasAprobadas,
                    'rechazadas' => $biomasasRechazadas,
                    'total' => $biomasasPendientes + $biomasasAprobadas + $biomasasRechazadas,
                ],
                'simulaciones' => [
                    'total' => $simulacionesTotal,
                ],
                'predictions' => [
                    'total' => $predictionsTotal,
                ],
                'actividad_reciente' => [
                    'biomasas' => $biomasasRecientes,
                    'simulaciones' => $simulacionesRecientes,
                    'predictions' => $predictionsRecientes,
                    'total' => $biomasasRecientes + $simulacionesRecientes + $predictionsRecientes,
                ],
            ]
        ]);
    }

    /**
     * Buscar actividades por ubicación o código.
     * Tipo soportado: 'ubicacion', 'provincia', 'municipio', 'latlng' o 'codigo' (texto libre)
     */
    public function getActivitiesByLocation($type, $value)
    {
        $value = urldecode($value);

        // Biomasa: buscar por campo `ubicacion` o `descripcion`
        $biomasas = Biomasa::withTrashed()
            ->with(['tipoBiomasa', 'user'])
            ->where(function ($q) use ($type, $value) {
                if (in_array($type, ['ubicacion', 'provincia', 'municipio'])) {
                    $q->where('ubicacion', 'LIKE', "%{$value}%");
                } else {
                    $q->where('ubicacion', 'LIKE', "%{$value}%")
                      ->orWhere('descripcion', 'LIKE', "%{$value}%");
                }
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($biomasa) {
                return [
                    'id' => $biomasa->id,
                    'tipo' => 'biomasa',
                    'fecha_creacion' => $biomasa->created_at,
                    'fecha_eliminacion' => $biomasa->deleted_at ?? null,
                    'tipo_biomasa' => $biomasa->tipoBiomasa->tipo_biomasa ?? 'N/A',
                    'area_m2' => $biomasa->area_m2,
                    'estado' => $biomasa->estado,
                    'descripcion' => $biomasa->descripcion,
                    'ubicacion' => $biomasa->ubicacion,
                    'status' => $biomasa->trashed() ? 'deleted' : 'active',
                ];
            });

        // Simulaciones: buscar por nombre o por centro de mapa si `latlng` (lat,lng)
        $simQuery = Simulacione::withTrashed();
        if ($type === 'latlng') {
            // valor esperado: "lat,lng"
            [$lat, $lng] = array_map('floatval', explode(',', $value));
            $delta = 0.02; // ~2km box
            $simQuery->whereBetween('map_center_lat', [$lat - $delta, $lat + $delta])
                     ->whereBetween('map_center_lng', [$lng - $delta, $lng + $delta]);
        } else {
            $simQuery->where('nombre', 'LIKE', "%{$value}%");
        }

        $simulaciones = $simQuery->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sim) {
                return [
                    'id' => $sim->id,
                    'tipo' => 'simulacion',
                    'fecha_creacion' => $sim->created_at,
                    'fecha_eliminacion' => $sim->deleted_at ?? null,
                    'nombre' => $sim->nombre,
                    'fecha_simulacion' => $sim->fecha,
                    'duracion' => $sim->duracion,
                    'estado' => $sim->estado,
                    'focos_activos' => $sim->focos_activos,
                    'map_center' => [$sim->map_center_lat, $sim->map_center_lng],
                    'status' => $sim->trashed() ? 'deleted' : 'active',
                ];
            });

        // Predictions: buscar texto en `meta` o `path` (fallback a LIKE)
        $predsQuery = Prediction::withTrashed();
        // Postgres: buscar dentro de JSON text
        try {
            $preds = $predsQuery->whereRaw("meta::text ILIKE ?", ["%{$value}%"]);
        } catch (\Exception $e) {
            $preds = $predsQuery->where('meta', 'LIKE', "%{$value}%");
        }

        $predictions = $preds->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pred) {
                return [
                    'id' => $pred->id,
                    'tipo' => 'prediccion',
                    'fecha_creacion' => $pred->created_at,
                    'fecha_eliminacion' => $pred->deleted_at ?? null,
                    'predicted_at' => $pred->predicted_at,
                    'foco_id' => $pred->foco_incendio_id,
                    'meta' => $pred->meta,
                    'status' => $pred->trashed() ? 'deleted' : 'active',
                ];
            });

        $all = $biomasas->concat($simulaciones)->concat($predictions)
            ->sortByDesc('fecha_creacion')
            ->values();

        return response()->json([
            'success' => true,
            'type' => $type,
            'query' => $value,
            'data' => [
                'biomasas' => $biomasas->values(),
                'simulaciones' => $simulaciones->values(),
                'predictions' => $predictions->values(),
                'todas' => $all,
            ],
            'totales' => [
                'biomasas' => $biomasas->count(),
                'simulaciones' => $simulaciones->count(),
                'predictions' => $predictions->count(),
                'total' => $all->count(),
            ]
        ]);
    }

    /**
     * Wrapper para búsquedas por `ubicacion`.
     */
    public function getActivitiesByUbicacion($value)
    {
        return $this->getActivitiesByLocation('ubicacion', $value);
    }

    /**
     * Obtener actividades del usuario autenticado
     */
    public function getMyActivities(Request $request)
    {
        $user = auth()->user();
        
        if (!$user || !$user->cedula_identidad) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado o sin CI registrado'
            ], 401);
        }

        return $this->getActivitiesByCi($user->cedula_identidad);
    }

    /**
     * Obtener estadísticas del usuario autenticado
     */
    public function getMyStats(Request $request)
    {
        $user = auth()->user();
        
        if (!$user || !$user->cedula_identidad) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado o sin CI registrado'
            ], 401);
        }

        return $this->getStatsByCi($user->cedula_identidad);
    }
}
