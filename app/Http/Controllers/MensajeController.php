<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversacion;
use Illuminate\Support\Facades\DB;

class MensajeController extends Controller
{
public function inbox()
    {
        $meId = Auth::id();

        // Conversaciones donde participo
        $conversaciones = Conversacion::query()
            ->whereHas('usuarios', fn($q) => $q->where('usuarios.usuarioid', $meId))
            ->with([
                'usuarios' => fn($q) => $q->where('usuarios.usuarioid', '!=', $meId),
                'mensajes' => fn($q) => $q->orderByDesc('fechaenvio')->limit(1),
            ])
            ->orderByDesc('updated_at')
            ->get();

        // Para iniciar chat con cualquiera
        $usuarios = Usuario::where('usuarioid', '!=', $meId)->orderBy('nombre')->get();

        return view('mensajes.chat.inbox', compact('conversaciones', 'usuarios'));
    }

    public function conversacion(Usuario $usuario)
    {
        $meId = Auth::id();

        // Buscar o crear conversación privada (me - usuario)
        $conv = $this->getOrCreatePrivateConversation($meId, $usuario->usuarioid);

        $mensajes = Mensaje::where('conversacionid', $conv->conversacionid)
            ->orderBy('fechaenvio')
            ->with('autor')
            ->get();

        // actualizar ultimo_leido del usuario actual
        DB::table('conversacion_usuarios')
            ->where('conversacionid', $conv->conversacionid)
            ->where('usuarioid', $meId)
            ->update(['ultimo_leido' => now()]);

        return view('mensajes.chat.conversacion', compact('usuario', 'mensajes', 'conv'));
    }

    public function enviar(Request $request, Usuario $usuario)
    {
        $meId = Auth::id();

        $request->validate([
            'asunto'    => 'required|string|max:150',
            'contenido' => 'required|string|max:5000',
        ]);

        if ($usuario->usuarioid == $meId) {
            return back()->with('error', 'No puedes enviarte mensajes a ti mismo.');
        }

        $conv = $this->getOrCreatePrivateConversation($meId, $usuario->usuarioid);

        Mensaje::create([
            'conversacionid' => $conv->conversacionid,
            'usuarioid'      => $meId,
            'asunto'         => $request->asunto,
            'contenido'      => $request->contenido,
            'fechaenvio'     => now(),
        ]);

        // actualizar updated_at de conversación para ordenar inbox
        $conv->touch();

        return redirect()->route('chat.conversacion', $usuario->usuarioid);
    }

    /**
     * Crea o recupera conversación privada entre 2 usuarios.
     */
    private function getOrCreatePrivateConversation(int $u1, int $u2): Conversacion
    {
        $min = min($u1, $u2);
        $max = max($u1, $u2);

        // Buscamos conversación que tenga exactamente ambos usuarios
        $conv = Conversacion::where('tipo', 'private')
            ->whereHas('usuarios', fn($q) => $q->where('usuarios.usuarioid', $min))
            ->whereHas('usuarios', fn($q) => $q->where('usuarios.usuarioid', $max))
            ->first();

        if ($conv) return $conv;

        return DB::transaction(function () use ($min, $max) {
            $conv = Conversacion::create(['tipo' => 'private']);

            DB::table('conversacion_usuarios')->insert([
                ['conversacionid' => $conv->conversacionid, 'usuarioid' => $min, 'ultimo_leido' => null],
                ['conversacionid' => $conv->conversacionid, 'usuarioid' => $max, 'ultimo_leido' => null],
            ]);

            return $conv;
        });
    }
}
