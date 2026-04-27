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
        $schema = $connection->getSchemaBuilder();

        // Evita fallos mientras el módulo aún no tenga migraciones aplicadas.
        if (!$schema->hasTable('users')) {
            return;
        }

        $emailColumn = $schema->hasColumn('users', 'correo_electronico') ? 'correo_electronico' : 'email';
        if (!$schema->hasColumn('users', $emailColumn)) {
            return;
        }

        $hasNombre = $schema->hasColumn('users', 'nombre');
        $hasApellido = $schema->hasColumn('users', 'apellido');
        $nameColumn = $schema->hasColumn('users', 'name') ? 'name' : null;

        $existing = $connection->table('users')->where('id', $authId)->first();
        $fullName = trim((string) ($user->name ?? $user->nombre ?? $user->usuario ?? ('Usuario ' . $authId)));
        $nameParts = preg_split('/\s+/', $fullName, 2);
        $nombre = $nameParts[0] ?? ('Usuario' . $authId);
        $apellido = $nameParts[1] ?? 'Logistica';
        $email = (string) ($user->email ?? $user->correo_electronico ?? $user->correo ?? ('usuario' . $authId . '@local.invalid'));

        $emailTaken = $connection->table('users')
            ->where($emailColumn, $email)
            ->where('id', '!=', $authId)
            ->exists();

        if ($emailTaken) {
            $email = 'logistica_user_' . $authId . '@local.invalid';
        }

        $payload = [
            $emailColumn => $email,
            'updated_at' => now(),
        ];
        if ($nameColumn) {
            $payload[$nameColumn] = $fullName;
        }
        if ($hasNombre) {
            $payload['nombre'] = $nombre;
        }
        if ($hasApellido) {
            $payload['apellido'] = $apellido;
        }

        if ($existing) {
            $connection->table('users')
                ->where('id', $authId)
                ->update($payload);
            return;
        }

        $insertPayload = [
            'id' => $authId,
            $emailColumn => $email,
            'password' => $user->password ?? Hash::make(Str::random(40)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if ($nameColumn) {
            $insertPayload[$nameColumn] = $fullName;
        }
        if ($hasNombre) {
            $insertPayload['nombre'] = $nombre;
        }
        if ($hasApellido) {
            $insertPayload['apellido'] = $apellido;
        }

        $connection->table('users')->insert($insertPayload);
    }
}
