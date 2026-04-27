<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UseLogisticaConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        DB::purge('logistica');
        DB::reconnect('logistica');
        $this->syncAuthenticatedUser();

        return $next($request);
    }

    private function syncAuthenticatedUser(): void
    {
        $user = Auth::user();
        $authId = Auth::id();

        if (!$user || !$authId) {
            return;
        }

        $connection = DB::connection('logistica');

        // Evita fallos mientras el módulo aún no tenga migraciones aplicadas.
        if (!$connection->getSchemaBuilder()->hasTable('users')) {
            return;
        }

        $existing = $connection->table('users')->where('id', $authId)->first();
        $displayName = $user->name ?? $user->nombre ?? $user->usuario ?? ('Usuario ' . $authId);
        $email = $user->email ?? $user->correo_electronico ?? $user->correo ?? ('usuario' . $authId . '@local.invalid');

        $emailTaken = $connection->table('users')
            ->where('email', $email)
            ->where('id', '!=', $authId)
            ->exists();

        if ($emailTaken) {
            $email = 'logistica_user_' . $authId . '@local.invalid';
        }

        if ($existing) {
            $connection->table('users')
                ->where('id', $authId)
                ->update([
                    'email' => $email,
                    'name' => $displayName,
                    'updated_at' => now(),
                ]);
            return;
        }

        $connection->table('users')->insert([
            'id' => $authId,
            'name' => $displayName,
            'email' => $email,
            'password' => $user->password ?? Hash::make(Str::random(40)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
