<?php

namespace App\Http\Controllers\SeguimientoVoluntarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ModuloController extends Controller
{
    public function index(): View
    {
        $connection = 'seguimiento';
        $db = DB::connection($connection);

        $voluntariosActivos = Schema::connection($connection)->hasTable('usuario')
            ? $db->table('usuario')->where('activo', true)->count()
            : 0;

        $voluntariosInactivos = Schema::connection($connection)->hasTable('usuario')
            ? $db->table('usuario')->where('activo', false)->count()
            : 0;

        $alertasRecientes = Schema::connection($connection)->hasTable('solicitudes_ayuda')
            ? $db->table('solicitudes_ayuda')->count()
            : 0;

        $evaluacionesCompletadas = Schema::connection($connection)->hasTable('evaluacion')
            ? $db->table('evaluacion')->count()
            : 0;

        $ultimosVoluntarios = collect();
        if (Schema::connection($connection)->hasTable('usuario')) {
            $query = $db->table('usuario');
            if (Schema::connection($connection)->hasColumn('usuario', 'created_at')) {
                $query->orderByDesc('created_at');
            } elseif (Schema::connection($connection)->hasColumn('usuario', 'id_usuario')) {
                $query->orderByDesc('id_usuario');
            }
            $ultimosVoluntarios = $query->limit(3)->get();
        }

        $ultimosReportes = collect();
        if (Schema::connection($connection)->hasTable('solicitudes_ayuda')) {
            $query = $db->table('solicitudes_ayuda');
            if (Schema::connection($connection)->hasColumn('solicitudes_ayuda', 'created_at')) {
                $query->orderByDesc('created_at');
            } elseif (Schema::connection($connection)->hasColumn('solicitudes_ayuda', 'id')) {
                $query->orderByDesc('id');
            }
            $ultimosReportes = $query->limit(3)->get();
        }

        $universidadesData = collect();
        if (Schema::connection($connection)->hasTable('universidad')) {
            $universidadesData = $db->table('universidad')
                ->select('id_universidad', 'nombre as label')
                ->orderBy('nombre')
                ->get()
                ->map(function ($row) use ($db, $connection) {
                    $total = Schema::connection($connection)->hasColumn('usuario', 'id_universidad')
                        ? $db->table('usuario')->where('id_universidad', $row->id_universidad)->count()
                        : 0;

                    return (object) ['label' => $row->label, 'total' => $total];
                });
        }

        $necesidadesData = collect();
        if (Schema::connection($connection)->hasTable('necesidad')) {
            $necesidadesData = $db->table('necesidad')
                ->select('nombre as label', 'id_necesidad')
                ->orderBy('nombre')
                ->get()
                ->map(function ($row) use ($db, $connection) {
                    $total = Schema::connection($connection)->hasTable('solicitudes_ayuda')
                        ? $db->table('solicitudes_ayuda')->where('tipo', $row->label)->count()
                        : 1;

                    return (object) ['label' => $row->label, 'total' => max(1, $total)];
                });
        }

        $capacitacionesData = collect();
        if (Schema::connection($connection)->hasTable('capacitacion')) {
            $capacitacionesData = $db->table('capacitacion')
                ->select('nombre as label', 'id_capacitacion')
                ->orderBy('nombre')
                ->get()
                ->map(fn ($row) => (object) ['label' => $row->label, 'total' => 1]);
        }

        $totalAdministradores = Schema::connection($connection)->hasTable('usuario')
            ? $db->table('usuario')->where('administrador', true)->count()
            : 0;

        $totalUniversidades = Schema::connection($connection)->hasTable('universidad')
            ? $db->table('universidad')->count()
            : 0;

        $consultasAbiertas = 0;
        if (Schema::connection($connection)->hasTable('consultas')) {
            $consultasAbiertas = Schema::connection($connection)->hasColumn('consultas', 'estado')
                ? $db->table('consultas')->whereIn('estado', ['abierta', 'en_proceso'])->count()
                : $db->table('consultas')->count();
        }

        $conversacionesChat = 0;
        if (Schema::connection($connection)->hasTable('chat_mensajes')) {
            if (Schema::connection($connection)->hasColumn('chat_mensajes', 'conversacion_id')) {
                $conversacionesChat = $db->table('chat_mensajes')
                    ->whereNotNull('conversacion_id')
                    ->distinct()
                    ->count('conversacion_id');
            } else {
                $conversacionesChat = $db->table('chat_mensajes')->count() > 0 ? 1 : 0;
            }
        }

        return view('fusion.modulos.seguimiento-voluntarios', [
            'voluntariosActivos' => $voluntariosActivos,
            'voluntariosInactivos' => $voluntariosInactivos,
            'alertasRecientes' => $alertasRecientes,
            'evaluacionesCompletadas' => $evaluacionesCompletadas,
            'ultimosVoluntarios' => $ultimosVoluntarios,
            'ultimosReportes' => $ultimosReportes,
            'universidadesData' => $universidadesData,
            'necesidadesData' => $necesidadesData,
            'capacitacionesData' => $capacitacionesData,
            'totalAdministradores' => $totalAdministradores,
            'totalUniversidades' => $totalUniversidades,
            'consultasAbiertas' => $consultasAbiertas,
            'conversacionesChat' => $conversacionesChat,
            'gestionCompleta' => \App\Support\AccessControl::gestionSeguimientoCompleta(auth()->user()),
        ]);
    }

    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'seguimiento-voluntarios-comunitarios',
        ]);
    }
}
