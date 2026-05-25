<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UseRescateConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        config(['database.default' => 'rescate']);
        DB::purge('rescate');
        DB::reconnect('rescate');
        $this->syncPersonProfile();

        return $next($request);
    }

    private function syncPersonProfile(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $authId = Auth::id();
        if (! $authId) {
            return;
        }

        $connection = DB::connection('rescate');
        if ($connection->table('people')->where('usuario_id', $authId)->exists()) {
            return;
        }

        $nombre = trim((string) ($user->name ?? $user->nombre ?? ''));
        if ($nombre === '') {
            $nombre = 'Usuario '.$authId;
        }

        $ci = (string) ($user->cedula_identidad ?? $user->ci ?? ('AUTO-'.$authId));

        $connection->table('people')->insert([
            'usuario_id' => $authId,
            'nombre' => $nombre,
            'ci' => $ci,
            'telefono' => $user->telefono ?? null,
            'es_cuidador' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
