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
        $tablasResumen = [
            'usuario' => 'Voluntarios',
            'evaluacion' => 'Evaluaciones',
            'capacitacion' => 'Capacitaciones',
            'necesidad' => 'Necesidades',
            'solicitudes_ayuda' => 'Ayudas Solicitadas',
            'chat_mensajes' => 'Mensajes de Chat',
        ];

        $resumen = [];
        foreach ($tablasResumen as $tabla => $label) {
            $resumen[] = [
                'label' => $label,
                'total' => Schema::connection($connection)->hasTable($tabla)
                    ? DB::connection($connection)->table($tabla)->count()
                    : 0,
            ];
        }

        $voluntariosRecientes = collect();
        if (Schema::connection($connection)->hasTable('usuario')) {
            $query = DB::connection($connection)->table('usuario');
            if (Schema::connection($connection)->hasColumn('usuario', 'created_at')) {
                $query->orderByDesc('created_at');
            } elseif (Schema::connection($connection)->hasColumn('usuario', 'id_usuario')) {
                $query->orderByDesc('id_usuario');
            }

            $voluntariosRecientes = $query->limit(10)->get();
        }

        return view('fusion.modulos.seguimiento-voluntarios', [
            'resumen' => $resumen,
            'voluntariosRecientes' => $voluntariosRecientes,
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
