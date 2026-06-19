<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\QueryException;
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

        $people = DB::connection('rescate')->table('people');

        if ($people->where('usuario_id', $authId)->exists()) {
            return;
        }

        $nombre = trim((string) ($user->name ?? $user->nombre ?? ''));
        if ($nombre === '') {
            $nombre = 'Usuario '.$authId;
        }

        $ci = $this->resolveUniqueCi($user, $authId);

        $existingByCi = $people->where('ci', $ci)->first();
        if ($existingByCi !== null) {
            if (empty($existingByCi->usuario_id)) {
                $people->where('id', $existingByCi->id)->update([
                    'usuario_id' => $authId,
                    'updated_at' => now(),
                ]);
            }

            return;
        }

        try {
            $people->insert([
                'usuario_id' => $authId,
                'nombre' => $nombre,
                'ci' => $ci,
                'telefono' => $user->telefono ?? null,
                'es_cuidador' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (QueryException $e) {
            if (! $this->isDuplicateCiViolation($e)) {
                throw $e;
            }

            $fallbackCi = 'CORE-'.$authId;
            if ($people->where('ci', $fallbackCi)->exists()) {
                return;
            }

            $people->insert([
                'usuario_id' => $authId,
                'nombre' => $nombre,
                'ci' => $fallbackCi,
                'telefono' => $user->telefono ?? null,
                'es_cuidador' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function resolveUniqueCi(object $user, int $authId): string
    {
        $attributes = method_exists($user, 'getAttributes')
            ? $user->getAttributes()
            : [];

        if (array_key_exists('cedula_identidad', $attributes) && $attributes['cedula_identidad'] !== null && $attributes['cedula_identidad'] !== '') {
            return (string) $attributes['cedula_identidad'];
        }

        if (array_key_exists('ci', $attributes) && $attributes['ci'] !== null && $attributes['ci'] !== '') {
            return (string) $attributes['ci'];
        }

        return 'CORE-'.$authId;
    }

    private function isDuplicateCiViolation(QueryException $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'duplicate key')
            || str_contains($message, 'unique constraint')
            || str_contains($message, '23505');
    }
}
