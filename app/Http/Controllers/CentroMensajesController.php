<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Donacion;
use App\Models\Conversacion;
use App\Models\Mensaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CentroMensajesController extends Controller
{
    // Pantalla para elegir usuario
    public function seleccionarUsuario()
    {
        $usuarios = Usuario::orderBy('nombre')->orderBy('apellido')->get();
        return view('mensajes.seleccionar_usuario', compact('usuarios'));
    }

    // Centro de mensajes para un usuario concreto
    public function centroPorUsuario(Request $request)
    {
        $request->validate([
            'usuarioid' => 'required|integer|exists:usuarios,usuarioid',
        ]);

        $usuario = Usuario::findOrFail($request->usuarioid);

        // Donaciones del usuario con campaña
        $donaciones = Donacion::with('campania')
            ->where('usuarioid', $usuario->usuarioid)
            ->get();

        /**
         * 1) Traer conversaciones donde participa el usuario
         * (pivot: conversacion_usuarios)
         */
        $conversacionIds = DB::table('conversacion_usuarios')
            ->where('usuarioid', $usuario->usuarioid)
            ->pluck('conversacionid');

        /**
         * 2) Traer mensajes de esas conversaciones (con autor + conversación + usuarios)
         * NOTA: ya no existe remitenteid/destinatarioid.
         */
        $mensajes = Mensaje::query()
            ->whereIn('conversacionid', $conversacionIds)
            ->with([
                'usuario:usuarioid,nombre,apellido',
                'conversacion.usuarios:usuarios.usuarioid,nombre,apellido',
            ])
            ->orderByDesc('fechaenvio')
            ->get()
            ->map(function ($m) {
                // Remitente = autor del mensaje
                $m->remitente_nombre = trim(($m->usuario->nombre ?? '') . ' ' . ($m->usuario->apellido ?? ''));

                // Destinatario = el "otro" usuario en la conversación (si es privada)
                $dest = null;
                if ($m->conversacion && $m->conversacion->usuarios) {
                    $dest = $m->conversacion->usuarios->firstWhere('usuarioid', '!=', $m->usuarioid);
                }

                $m->destinatario_nombre = $dest
                    ? trim($dest->nombre . ' ' . $dest->apellido)
                    : null;

                return $m;
            });

        /**
         * 3) Respuestas agrupadas por mensaje (esto sigue igual)
         */
        $respuestas = DB::table('respuestasmensajes')
            ->leftJoin('usuarios', 'respuestasmensajes.usuarioid', '=', 'usuarios.usuarioid')
            ->whereIn('mensajeid', $mensajes->pluck('mensajeid'))
            ->select(
                'respuestasmensajes.*',
                DB::raw("usuarios.nombre || ' ' || usuarios.apellido as usuario_nombre")
            )
            ->orderBy('fecharespuesta')
            ->get()
            ->groupBy('mensajeid');

        return view('mensajes.centro_usuario', compact(
            'usuario',
            'donaciones',
            'mensajes',
            'respuestas'
        ));
    }
}
