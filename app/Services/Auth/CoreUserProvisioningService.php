<?php

namespace App\Services\Auth;

use App\Models\Usuario;
use App\Support\AccessControl;
use App\Support\UnifiedPostgres;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Inventario\Models\Usuario as InventarioUsuario;
use Spatie\Permission\Models\Role;

/**
 * Garantiza que toda identidad operativa exista en core.usuarios para el login central (/login).
 */
class CoreUserProvisioningService
{
    public function isRequired(): bool
    {
        return UnifiedPostgres::enabled();
    }

    /**
     * Sincroniza un usuario del módulo inventario hacia core.usuarios.
     */
    public function syncFromInventario(InventarioUsuario $inventarioUser, ?string $roleName = null): ?Usuario
    {
        if (! $this->isRequired()) {
            return null;
        }

        $email = $this->normalizeEmail($inventarioUser->correo);
        if ($email === '') {
            return null;
        }

        if ($roleName === null) {
            $roleName = $inventarioUser->primary_role_name;
        }

        $payload = [
            'nombre' => Str::limit((string) $inventarioUser->nombres, 50, ''),
            'apellido' => Str::limit((string) $inventarioUser->apellidos, 50, ''),
            'email' => $email,
            'contrasena' => $this->resolvePasswordHash((string) $inventarioUser->contrasena),
            'telefono' => $inventarioUser->telefono ? Str::limit((string) $inventarioUser->telefono, 20, '') : null,
            'activo' => ($inventarioUser->estado ?? 'Activo') === 'Activo',
        ];

        $coreUser = Usuario::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($coreUser) {
            $coreUser->update($payload);
        } else {
            $coreUser = Usuario::create($payload);
        }

        if ($roleName) {
            $this->assignCoreRole($coreUser, $roleName);
        }

        return $coreUser;
    }

    public function deactivateByEmail(?string $email): void
    {
        if (! $this->isRequired()) {
            return;
        }

        $normalized = $this->normalizeEmail($email);
        if ($normalized === '') {
            return;
        }

        Usuario::query()
            ->whereRaw('LOWER(email) = ?', [$normalized])
            ->update(['activo' => false]);
    }

    /**
     * @return array{synced:int, skipped:int, errors:int}
     */
    public function syncAllInventarioUsers(): array
    {
        $stats = ['synced' => 0, 'skipped' => 0, 'errors' => 0];

        if (! $this->isRequired()) {
            return $stats;
        }

        if (! DB::connection('inventario')->getSchemaBuilder()->hasTable('usuarios')) {
            return $stats;
        }

        InventarioUsuario::query()->orderBy('id_usuario')->chunk(100, function ($users) use (&$stats) {
            foreach ($users as $user) {
                try {
                    $email = $this->normalizeEmail($user->correo);
                    if ($email === '') {
                        $stats['skipped']++;

                        continue;
                    }

                    $this->syncFromInventario($user);
                    $stats['synced']++;
                } catch (\Throwable) {
                    $stats['errors']++;
                }
            }
        });

        return $stats;
    }

    public function assignCoreRole(Usuario $user, string $roleName): void
    {
        $canonical = AccessControl::normalizeLegacyRole($roleName);
        if ($canonical === null || $canonical === '') {
            return;
        }

        Role::query()->firstOrCreate(
            ['name' => $canonical, 'guard_name' => AccessControl::GUARD],
            ['descripcion' => 'Rol sincronizado desde módulo']
        );

        AccessControl::syncSingleRole($user, $canonical);
    }

    public function normalizeEmail(?string $email): string
    {
        return strtolower(trim((string) $email));
    }

    public function isBcryptHash(?string $hash): bool
    {
        if (! is_string($hash) || $hash === '') {
            return false;
        }

        return str_starts_with($hash, '$2y$')
            || str_starts_with($hash, '$2a$')
            || str_starts_with($hash, '$2b$');
    }

    private function resolvePasswordHash(string $hash): string
    {
        if ($this->isBcryptHash($hash)) {
            return $hash;
        }

        return Hash::make($hash !== '' ? $hash : Str::random(32));
    }
}
