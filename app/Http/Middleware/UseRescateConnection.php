<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UseRescateConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        DB::purge('rescate');
        DB::reconnect('rescate');
        $this->syncAuthenticatedUser();

        return $next($request);
    }

    private function syncAuthenticatedUser(): void
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }
        $authId = Auth::id();
        if (!$authId) {
            return;
        }

        $connection = DB::connection('rescate');
        $existingUser = $connection->table('users')->where('id', $authId)->first();

        $email = $user->email ?? $user->correo_electronico ?? $user->correo ?? ('usuario' . $authId . '@local.invalid');
        $emailTaken = $connection->table('users')
            ->where('email', $email)
            ->where('id', '!=', $authId)
            ->exists();
        if ($emailTaken) {
            $email = 'rescate_user_' . $authId . '@local.invalid';
        }

        if ($existingUser) {
            $connection->table('users')
                ->where('id', $authId)
                ->update([
                    'email' => $email,
                    'password' => $this->passwordHashForShadowUser($user, $existingUser->password),
                    'updated_at' => now(),
                ]);
        } else {
            $connection->table('users')->insert([
                'id' => $authId,
                'email' => $email,
                'password' => $this->passwordHashForShadowUser($user, null),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $personExists = $connection->table('people')->where('usuario_id', $authId)->exists();
        if ($personExists) {
            return;
        }

        $nombre = trim((string) ($user->name ?? $user->nombre ?? $user->usuario ?? ''));
        if ($nombre === '') {
            $nombre = 'Usuario ' . $authId;
        }

        $ci = (string) ($user->cedula_identidad ?? $user->ci ?? ('AUTO-' . $authId));

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

    /**
     * Usa el hash de contraseña del modelo autenticado (p. ej. contrasena en {@see \App\Models\Usuario}).
     */
    private function passwordHashForShadowUser(object $user, ?string $existingPassword): string
    {
        if ($user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
            $fromAuth = (string) $user->getAuthPassword();
            if ($fromAuth !== '') {
                return $fromAuth;
            }
        }

        $plain = (string) ($user->password ?? '');
        if ($plain !== '') {
            return $plain;
        }

        if ($existingPassword !== null && $existingPassword !== '') {
            return $existingPassword;
        }

        return Hash::make(Str::random(40));
    }
}
