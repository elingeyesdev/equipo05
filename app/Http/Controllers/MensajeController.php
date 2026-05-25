<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversacion;
use App\Support\UnifiedPostgres;
use Illuminate\Support\Facades\DB;

class MensajeController extends Controller
{
    public function index()
    {
        return redirect()->route('chat.inbox');
    }

    public function create()
    {
        $usuarios = Usuario::where('usuarioid', '!=', Auth::id())->orderBy('nombre')->get();

        return view('mensajes.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $meId = Auth::id();

        $usuariosTable = UnifiedPostgres::enabled() ? 'core.usuarios' : 'usuarios';
        $usuariosKey = UnifiedPostgres::enabled() ? 'usuarioid' : 'usuarioid';

        $data = $request->validate([
            'destinatarioid' => 'nullable|integer|exists:'.$usuariosTable.','.$usuariosKey,
            'asunto'         => 'required|string|max:150',
            'contenido'      => 'required|string|max:5000',
            'fechaenvio'     => 'nullable|date',
            'leido'          => 'nullable|boolean',
            'respondido'     => 'nullable|boolean',
        ]);

        if (empty($data['destinatarioid'])) {
            return back()->withInput()->with('error', 'Selecciona un destinatario o usa el chat directo.');
        }

        if ((int) $data['destinatarioid'] === $meId) {
            return back()->withInput()->with('error', 'No puedes enviarte mensajes a ti mismo.');
        }

        $destinatario = Usuario::findOrFail($data['destinatarioid']);
        $conv = $this->getOrCreatePrivateConversation($meId, $destinatario->usuarioid);

        Mensaje::create([
            'conversacionid' => $conv->conversacionid,
            'usuarioid'      => $meId,
            'asunto'         => $data['asunto'],
            'contenido'      => $data['contenido'],
            'fechaenvio'     => $data['fechaenvio'] ?? now(),
            'leido'          => $request->boolean('leido'),
        ]);

        $conv->touch();

        return redirect()->route('chat.conversacion', $destinatario->usuarioid)
            ->with('success', 'Mensaje enviado correctamente.');
    }

    public function show($id)
    {
        $mensaje = Mensaje::findOrFail($id);
        $mensaje->load(['autor', 'conversacion']);

        return view('mensajes.show', compact('mensaje'));
    }

    public function edit($id)
    {
        $mensaje = Mensaje::findOrFail($id);
        $usuarios = Usuario::orderBy('nombre')->get();

        return view('mensajes.edit', compact('mensaje', 'usuarios'));
    }

    public function update(Request $request, $id)
    {
        $mensaje = Mensaje::findOrFail($id);

        $data = $request->validate([
            'asunto'    => 'required|string|max:150',
            'contenido' => 'required|string|max:5000',
            'leido'     => 'nullable|boolean',
        ]);

        $mensaje->update([
            'asunto'    => $data['asunto'],
            'contenido' => $data['contenido'],
            'leido'     => $request->boolean('leido'),
        ]);

        return redirect()->route('mensajes.show', $mensaje->mensajeid)
            ->with('success', 'Mensaje actualizado.');
    }

    public function destroy($id)
    {
        Mensaje::findOrFail($id)->delete();

        return redirect()->route('chat.inbox')->with('success', 'Mensaje eliminado.');
    }

    public function inbox()
    {
        $meId = Auth::id();

        $conversaciones = Conversacion::query()
            ->whereHas('usuarios', fn ($q) => $q->where('usuarios.usuarioid', $meId))
            ->with([
                'mensajes' => fn ($q) => $q->orderByDesc('fechaenvio')->limit(1),
            ])
            ->orderByDesc('updated_at')
            ->get();

        if ($conversaciones->isNotEmpty()) {
            $pivotRows = $this->transparenciaDb()->table('conversacion_usuarios')
                ->whereIn('conversacionid', $conversaciones->pluck('conversacionid'))
                ->get()
                ->groupBy('conversacionid');

            $otherIds = $pivotRows->flatten(1)
                ->where('usuarioid', '!=', $meId)
                ->pluck('usuarioid')
                ->unique()
                ->values();

            $usuariosById = Usuario::query()
                ->whereIn('usuarioid', $otherIds)
                ->get()
                ->keyBy('usuarioid');

            $conversaciones = $conversaciones
                ->map(function (Conversacion $c) use ($pivotRows, $meId, $usuariosById) {
                    $participants = $pivotRows->get($c->conversacionid, collect());
                    $otherId = $participants->firstWhere('usuarioid', '!=', $meId)?->usuarioid;
                    $c->setRelation('otroUsuario', $otherId ? $usuariosById->get($otherId) : null);

                    return $c;
                })
                ->filter(fn (Conversacion $c) => $c->otroUsuario !== null)
                ->values();
        }

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
        $this->transparenciaDb()->table('conversacion_usuarios')
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

            $this->transparenciaDb()->table('conversacion_usuarios')->insert([
                ['conversacionid' => $conv->conversacionid, 'usuarioid' => $min, 'ultimo_leido' => null],
                ['conversacionid' => $conv->conversacionid, 'usuarioid' => $max, 'ultimo_leido' => null],
            ]);

            return $conv;
        });
    }

    private function transparenciaDb(): \Illuminate\Database\Connection
    {
        return DB::connection(UnifiedPostgres::transparenciaConnection());
    }
}
