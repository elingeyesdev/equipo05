<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UseIncendiosConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        DB::purge('incendios');
        DB::reconnect('incendios');
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

        $connection = DB::connection('incendios');
        $existing = $connection->table('users')->where('id', $authId)->first();
        $displayName = $user->name ?? $user->nombre ?? $user->usuario ?? ('Usuario ' . $authId);
        $email = $user->email ?? $user->correo_electronico ?? $user->correo ?? ('usuario' . $authId . '@local.invalid');

        if ($existing) {
            $connection->table('users')
                ->where('id', $authId)
                ->update([
                    'email' => $email ?: $existing->email,
                    'name' => $displayName ?: $existing->name,
                    'password' => $this->passwordHashForShadowUser($user, $existing->password),
                    'updated_at' => now(),
                ]);
            return;
        }

        $emailTaken = $connection->table('users')
            ->where('email', $email)
            ->where('id', '!=', $authId)
            ->exists();
        if ($emailTaken) {
            $email = 'incendios_user_' . $authId . '@local.invalid';
        }

        $connection->table('users')->insert([
            'id' => $authId,
            'name' => $displayName,
            'email' => $email,
            'password' => $this->passwordHashForShadowUser($user, null),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Replica el hash de acceso del usuario central (p. ej. {@see \App\Models\Usuario} con contrasena)
     * para que la fila sombra en incendios sea coherente con el login unificado.
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
