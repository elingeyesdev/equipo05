<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Support\AccessControl;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AccessControlSeeder extends Seeder
{
    /** @var list<string> */
    private array $warnings = [];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->seedPermissions();
        $this->seedRoles();
        $this->assignPermissionsToRoles();
        $this->migrateLegacyUserRoles();
        $this->removeDeprecatedRoles();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->warnings as $warning) {
            $this->command?->warn($warning);
        }

        $this->command?->info('AccessControlSeeder: roles y permisos actualizados.');
    }

    private function seedPermissions(): void
    {
        foreach (AccessControl::flattenPermissions() as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => AccessControl::GUARD]
            );
        }
    }

    private function seedRoles(): void
    {
        $descriptions = [
            'Administrador' => 'Administración del sistema y soporte global.',
            'Operador de Incendios' => 'Responsable del módulo de monitoreo de incendios.',
            'Almacenero' => 'Responsable de inventario y donaciones físicas.',
            'Coordinador Logístico' => 'Responsable de logística y transportación.',
            'Coordinador de Voluntarios' => 'Responsable de seguimiento de voluntarios.',
            'Jefe de Cuadrilla' => 'Responsable de cuadrillas y recursos de campo.',
            'Rescatista' => 'Rescate animal en campo.',
            'Veterinario' => 'Atención médica animal.',
            'Cuidador' => 'Cuidado diario de animales.',
            'Voluntario' => 'Panel operativo limitado de voluntario.',
            'Donante' => 'Portal de donaciones propias.',
            'Ciudadano' => 'Portal comunitario y reportes propios.',
        ];

        foreach (AccessControl::FINAL_ROLES as $roleName) {
            Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => AccessControl::GUARD],
                ['descripcion' => $descriptions[$roleName] ?? $roleName]
            );
        }
    }

    private function assignPermissionsToRoles(): void
    {
        foreach (AccessControl::rolePermissionMap() as $roleName => $permissions) {
            $role = Role::findByName($roleName, AccessControl::GUARD);
            $role->syncPermissions($permissions);
        }
    }

    private function migrateLegacyUserRoles(): void
    {
        Usuario::query()->with('roles')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $currentRoles = $user->getRoleNames()->all();

                if ($currentRoles === []) {
                    continue;
                }

                if (count($currentRoles) > 1) {
                    $this->warnings[] = "Usuario {$user->email}: tenía múltiples roles (".implode(', ', $currentRoles).'). Se conservó solo el primero.';
                }

                $legacy = $currentRoles[0];

                if (in_array($legacy, ['Reportes', 'reportes'], true)) {
                    $user->syncRoles([]);
                    $this->warnings[] = "Usuario {$user->email}: rol Reportes eliminado. Requiere reasignación manual.";

                    continue;
                }

                if (in_array($legacy, ['Visitante', 'visitante'], true)) {
                    $user->syncRoles([]);
                    $this->warnings[] = "Usuario {$user->email}: rol Visitante eliminado (acceso público sin login).";

                    continue;
                }

                if (in_array($legacy, ['encargado'], true)) {
                    AccessControl::syncSingleRole($user, 'Operador de Incendios');
                    $this->warnings[] = "Usuario {$user->email}: rol encargado migrado temporalmente a Operador de Incendios. Revisar asignación manual.";

                    continue;
                }

                $normalized = AccessControl::normalizeLegacyRole($legacy);
                if ($normalized) {
                    AccessControl::syncSingleRole($user, $normalized);
                } else {
                    $user->syncRoles([]);
                    $this->warnings[] = "Usuario {$user->email}: rol '{$legacy}' no reconocido. Quedó sin rol.";
                }
            }
        });
    }

    private function removeDeprecatedRoles(): void
    {
        $keep = AccessControl::FINAL_ROLES;

        Role::query()
            ->where('guard_name', AccessControl::GUARD)
            ->whereNotIn('name', $keep)
            ->each(function (Role $role) {
                $usersCount = DB::table('model_has_roles')
                    ->where('role_id', $role->id)
                    ->count();

                if ($usersCount > 0) {
                    $this->warnings[] = "Rol obsoleto '{$role->name}' aún tiene {$usersCount} usuario(s). No se eliminó.";

                    return;
                }

                $role->permissions()->detach();
                $role->delete();
            });
    }
}
