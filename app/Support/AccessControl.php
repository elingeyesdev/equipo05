<?php

namespace App\Support;

use App\Models\Usuario;
use Spatie\Permission\Models\Role;

/**
 * Catálogo central de roles, permisos y reglas de acceso entre módulos.
 */
class AccessControl
{
    public const GUARD = 'web';

    /** Roles asignados al registrarse desde el login público (usuario común). */
    public const PUBLIC_COMMUNITY_ROLES = ['Ciudadano', 'Donante'];

    /** @var list<string> */
    public const FINAL_ROLES = [
        'Administrador',
        'Operador de Incendios',
        'Almacenero',
        'Coordinador Logístico',
        'Coordinador de Voluntarios',
        'Jefe de Cuadrilla',
        'Rescatista',
        'Veterinario',
        'Cuidador',
        'Voluntario',
        'Donante',
        'Ciudadano',
    ];

    /** Roles eliminados del sistema operativo. */
    public const DEPRECATED_ROLES = [
        'Reportes',
        'Visitante',
        'encargado',
        'Almacenista',
        'admin',
        'administrador',
        'voluntario',
        'donante',
        'ciudadano',
        'rescatista',
        'veterinario',
        'cuidador',
    ];

    /** Rol responsable principal por módulo. */
    public const MODULE_PRIMARY_ROLE = [
        'admin' => 'Administrador',
        'inventario' => 'Almacenero',
        'incendios' => 'Operador de Incendios',
        'logistica' => 'Coordinador Logístico',
        'seguimiento' => 'Coordinador de Voluntarios',
        'cuadrillas' => 'Jefe de Cuadrilla',
        'rescate' => 'Rescatista',
        'donante' => 'Donante',
        'ciudadano' => 'Ciudadano',
    ];

    /**
     * Roles con acceso al módulo (entrada por URL). Administrador siempre incluido vía código.
     *
     * @var array<string, list<string>>
     */
    public const MODULE_ACCESS_ROLES = [
        'admin' => ['Administrador'],
        'inventario' => ['Almacenero', 'Coordinador Logístico', 'Donante'],
        'incendios' => ['Operador de Incendios', 'Jefe de Cuadrilla', 'Rescatista', 'Coordinador de Voluntarios', 'Coordinador Logístico', 'Almacenero', 'Ciudadano'],
        'logistica' => ['Coordinador Logístico', 'Almacenero', 'Operador de Incendios'],
        'seguimiento' => ['Administrador', 'Coordinador de Voluntarios', 'Voluntario', 'Operador de Incendios'],
        'cuadrillas' => ['Jefe de Cuadrilla', 'Operador de Incendios'],
        'rescate' => ['Rescatista', 'Veterinario', 'Cuidador', 'Ciudadano'],
        'donante' => ['Donante'],
        'ciudadano' => ['Ciudadano'],
    ];

    /** @return array<string, list<string>> */
    public static function allPermissionsByModule(): array
    {
        return [
            'admin' => [
                'admin.dashboard.ver',
                'admin.usuarios.gestionar',
                'admin.roles.gestionar',
                'admin.configuracion.gestionar',
            ],
            'incendios' => [
                'incendios.dashboard.ver',
                'incendios.alertas.publicar',
                'incendios.reportes_ciudadanos.ver',
                'incendios.reportes_ciudadanos.validar',
                'incendios.focos.gestionar',
                'incendios.estados.actualizar',
                'incendios.reportes.ver',
            ],
            'inventario' => [
                'inventario.dashboard.ver',
                'inventario.usuarios.gestionar',
                'inventario.campanas.gestionar',
                'inventario.categorias.gestionar',
                'inventario.productos.gestionar',
                'inventario.donaciones.registrar',
                'inventario.donantes.gestionar',
                'inventario.puntos.gestionar',
                'inventario.almacenes.gestionar',
                'inventario.stock.gestionar',
                'inventario.recoleccion.gestionar',
                'inventario.paquetes.gestionar',
                'inventario.paquetes.ver',
                'inventario.salidas.registrar',
                'inventario.reportes.ver',
            ],
            'logistica' => [
                'logistica.dashboard.ver',
                'logistica.solicitudes.gestionar',
                'logistica.vehiculos.gestionar',
                'logistica.conductores.gestionar',
                'logistica.destinos.gestionar',
                'logistica.entregas.gestionar',
                'logistica.seguimiento.gestionar',
                'logistica.reportes.ver',
            ],
            'voluntarios' => [
                'voluntarios.dashboard.ver',
                'voluntarios.gestionar',
                'voluntarios.evaluaciones.gestionar',
                'voluntarios.capacitaciones.gestionar',
                'voluntarios.necesidades.gestionar',
                'voluntarios.asignaciones.gestionar',
                'voluntarios.reportes.ver',
            ],
            'cuadrillas' => [
                'cuadrillas.dashboard.ver',
                'cuadrillas.equipos.gestionar',
                'cuadrillas.recursos.gestionar',
                'cuadrillas.reportes_campo.gestionar',
                'cuadrillas.cursos.gestionar',
                'cuadrillas.kardex.gestionar',
                'cuadrillas.reportes.ver',
            ],
            'rescate' => [
                'rescate.dashboard.ver',
                'rescate.reportes.ver',
                'rescate.rescates.gestionar',
                'rescate.traslados.gestionar',
                'rescate.animales.ver',
            ],
            'veterinaria' => [
                'veterinaria.dashboard.ver',
                'veterinaria.animales.ver',
                'veterinaria.evaluaciones.gestionar',
                'veterinaria.diagnosticos.gestionar',
                'veterinaria.tratamientos.gestionar',
                'veterinaria.liberaciones.gestionar',
                'veterinaria.reportes.ver',
            ],
            'cuidados' => [
                'cuidados.dashboard.ver',
                'cuidados.animales.ver',
                'cuidados.alimentacion.registrar',
                'cuidados.cuidados_diarios.registrar',
                'cuidados.observaciones.registrar',
            ],
            'ciudadano' => [
                'ciudadano.alertas.ver',
                'ciudadano.incendios.reportar',
                'ciudadano.reportes_propios.ver',
                'ciudadano.solicitudes_ayuda.crear',
                'ciudadano.animales.reportar',
                'comunidad.almacenes.ver',
                'comunidad.mapa_territorial.ver',
            ],
            'donante' => [
                'donante.campanas.ver',
                'donante.puntos.ver',
                'donante.donaciones.crear',
                'donante.donaciones_propias.ver',
                'donante.comprobantes.ver',
                'donante.perfil.gestionar',
                'comunidad.almacenes.ver',
                'comunidad.mapa_territorial.ver',
            ],
            'voluntario_panel' => [
                'voluntario.panel.ver',
                'voluntario.tareas.ver',
                'voluntario.capacitaciones.ver',
                'voluntario.participacion.registrar',
            ],
        ];
    }

    /** @return array<string, list<string>> */
    public static function rolePermissionMap(): array
    {
        return [
            'Administrador' => self::flattenPermissions(),
            'Operador de Incendios' => [
                'incendios.dashboard.ver', 'incendios.alertas.publicar',
                'incendios.reportes_ciudadanos.ver', 'incendios.reportes_ciudadanos.validar',
                'incendios.focos.gestionar', 'incendios.estados.actualizar', 'incendios.reportes.ver',
                'cuadrillas.dashboard.ver', 'cuadrillas.reportes.ver',
                'logistica.dashboard.ver', 'logistica.reportes.ver',
                'rescate.dashboard.ver', 'rescate.reportes.ver',
                'inventario.dashboard.ver', 'inventario.reportes.ver',
            ],
            'Almacenero' => [
                'inventario.dashboard.ver',
                'inventario.usuarios.gestionar',
                'inventario.campanas.gestionar',
                'inventario.categorias.gestionar',
                'inventario.productos.gestionar',
                'inventario.donaciones.registrar',
                'inventario.donantes.gestionar',
                'inventario.puntos.gestionar',
                'inventario.almacenes.gestionar',
                'inventario.stock.gestionar',
                'inventario.recoleccion.gestionar',
                'inventario.paquetes.gestionar',
                'inventario.paquetes.ver',
                'inventario.salidas.registrar',
                'inventario.reportes.ver',
                'incendios.dashboard.ver',
                'logistica.dashboard.ver',
                'logistica.solicitudes.gestionar',
            ],
            'Coordinador Logístico' => [
                'logistica.dashboard.ver', 'logistica.solicitudes.gestionar',
                'logistica.vehiculos.gestionar', 'logistica.conductores.gestionar',
                'logistica.destinos.gestionar', 'logistica.entregas.gestionar',
                'logistica.seguimiento.gestionar', 'logistica.reportes.ver',
                'inventario.paquetes.ver', 'inventario.dashboard.ver',
                'incendios.dashboard.ver',
            ],
            'Coordinador de Voluntarios' => [
                'voluntarios.dashboard.ver', 'voluntarios.gestionar',
                'voluntarios.evaluaciones.gestionar', 'voluntarios.capacitaciones.gestionar',
                'voluntarios.necesidades.gestionar', 'voluntarios.asignaciones.gestionar',
                'voluntarios.reportes.ver',
                'incendios.dashboard.ver',
            ],
            'Jefe de Cuadrilla' => [
                'cuadrillas.dashboard.ver', 'cuadrillas.equipos.gestionar',
                'cuadrillas.recursos.gestionar', 'cuadrillas.reportes_campo.gestionar',
                'cuadrillas.cursos.gestionar', 'cuadrillas.kardex.gestionar',
                'cuadrillas.reportes.ver',
                'incendios.dashboard.ver',
            ],
            'Rescatista' => [
                'rescate.dashboard.ver', 'rescate.reportes.ver',
                'rescate.rescates.gestionar', 'rescate.traslados.gestionar',
                'rescate.animales.ver',
                'incendios.dashboard.ver',
                'ciudadano.animales.reportar',
            ],
            'Veterinario' => [
                'veterinaria.dashboard.ver', 'veterinaria.animales.ver',
                'veterinaria.evaluaciones.gestionar', 'veterinaria.diagnosticos.gestionar',
                'veterinaria.tratamientos.gestionar', 'veterinaria.liberaciones.gestionar',
                'veterinaria.reportes.ver',
                'rescate.dashboard.ver', 'rescate.animales.ver',
                'cuidados.dashboard.ver', 'cuidados.animales.ver',
            ],
            'Cuidador' => [
                'cuidados.dashboard.ver', 'cuidados.animales.ver',
                'cuidados.alimentacion.registrar', 'cuidados.cuidados_diarios.registrar',
                'cuidados.observaciones.registrar',
                'veterinaria.animales.ver',
                'rescate.dashboard.ver', 'rescate.animales.ver',
            ],
            'Voluntario' => [
                'voluntario.panel.ver', 'voluntario.tareas.ver',
                'voluntario.capacitaciones.ver', 'voluntario.participacion.registrar',
            ],
            'Donante' => [
                'donante.campanas.ver', 'donante.puntos.ver',
                'donante.donaciones.crear', 'donante.donaciones_propias.ver',
                'donante.comprobantes.ver', 'donante.perfil.gestionar',
                'comunidad.almacenes.ver', 'comunidad.mapa_territorial.ver',
            ],
            'Ciudadano' => [
                'ciudadano.alertas.ver', 'ciudadano.incendios.reportar',
                'ciudadano.reportes_propios.ver', 'ciudadano.solicitudes_ayuda.crear',
                'ciudadano.animales.reportar',
                'incendios.dashboard.ver',
                'comunidad.almacenes.ver', 'comunidad.mapa_territorial.ver',
            ],
        ];
    }

    /** @return list<string> */
    public static function flattenPermissions(): array
    {
        $all = [];
        foreach (self::allPermissionsByModule() as $perms) {
            $all = array_merge($all, $perms);
        }

        return array_values(array_unique($all));
    }

    public static function normalizeLegacyRole(string $roleName): ?string
    {
        $roleName = trim($roleName);
        if ($roleName === '') {
            return null;
        }

        $map = [
            'admin' => 'Administrador',
            'administrador' => 'Administrador',
            'Administrador' => 'Administrador',
            'encargado' => 'Operador de Incendios',
            'Operador de Incendios' => 'Operador de Incendios',
            'almacenero' => 'Almacenero',
            'Almacenero' => 'Almacenero',
            'almacenista' => 'Almacenero',
            'Almacenista' => 'Almacenero',
            'Reportes' => null,
            'reportes' => null,
            'Visitante' => null,
            'visitante' => null,
            'voluntario' => 'Voluntario',
            'Voluntario' => 'Voluntario',
            'donante' => 'Donante',
            'Donante' => 'Donante',
            'ciudadano' => 'Ciudadano',
            'Ciudadano' => 'Ciudadano',
            'rescatista' => 'Rescatista',
            'Rescatista' => 'Rescatista',
            'veterinario' => 'Veterinario',
            'Veterinario' => 'Veterinario',
            'cuidador' => 'Cuidador',
            'Cuidador' => 'Cuidador',
        ];

        if (array_key_exists($roleName, $map)) {
            return $map[$roleName];
        }

        $lower = strtolower($roleName);
        if (array_key_exists($lower, $map)) {
            return $map[$lower];
        }

        return in_array($roleName, self::FINAL_ROLES, true) ? $roleName : null;
    }

    public static function syncSingleRole(Usuario $user, string $roleName): void
    {
        $canonical = self::normalizeLegacyRole($roleName);
        if ($canonical === null) {
            $user->syncRoles([]);

            return;
        }

        $user->syncRoles([$canonical]);
    }

    public static function syncPublicCommunityRoles(Usuario $user): void
    {
        foreach (self::PUBLIC_COMMUNITY_ROLES as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => self::GUARD,
            ]);
        }

        $user->syncRoles(self::PUBLIC_COMMUNITY_ROLES);
    }

    public static function isPublicCommunityUser(?Usuario $user): bool
    {
        if (! $user) {
            return false;
        }

        $userRoles = $user->getRoleNames()
            ->map(fn ($role) => self::normalizeLegacyRole($role))
            ->filter()
            ->unique()
            ->values();

        if ($userRoles->isEmpty()) {
            return false;
        }

        $staffRoles = array_values(array_filter(
            self::FINAL_ROLES,
            fn ($role) => ! in_array($role, self::PUBLIC_COMMUNITY_ROLES, true)
        ));

        foreach ($staffRoles as $staffRole) {
            if ($userRoles->contains($staffRole)) {
                return false;
            }
        }

        return $userRoles->every(
            fn ($role) => in_array($role, self::PUBLIC_COMMUNITY_ROLES, true)
        );
    }

    public static function userCanAccessModule(?Usuario $user, string $module): bool
    {
        if (! $user) {
            return false;
        }

        if (self::isPublicCommunityUser($user)) {
            $allowed = self::MODULE_ACCESS_ROLES[$module] ?? [];
            foreach (self::PUBLIC_COMMUNITY_ROLES as $role) {
                if (self::userHasRole($user, $role) && in_array($role, $allowed, true)) {
                    return true;
                }
            }

            return false;
        }

        if ($user->hasRole('Administrador')) {
            return true;
        }

        $allowed = self::MODULE_ACCESS_ROLES[$module] ?? [];

        foreach ($allowed as $role) {
            if (self::userHasRole($user, $role)) {
                return true;
            }
        }

        return false;
    }

    /** @return list<string> */
    public static function roleAliases(string $canonicalRole): array
    {
        return match ($canonicalRole) {
            'Administrador' => ['Administrador', 'admin', 'administrador'],
            'Operador de Incendios' => ['Operador de Incendios', 'encargado'],
            'Almacenero' => ['Almacenero', 'Almacenista', 'almacenero', 'almacenista'],
            'Rescatista' => ['Rescatista', 'rescatista'],
            'Veterinario' => ['Veterinario', 'veterinario'],
            'Cuidador' => ['Cuidador', 'cuidador'],
            'Ciudadano' => ['Ciudadano', 'ciudadano'],
            'Voluntario' => ['Voluntario', 'voluntario'],
            'Donante' => ['Donante', 'donante'],
            default => in_array($canonicalRole, self::FINAL_ROLES, true) ? [$canonicalRole] : [$canonicalRole],
        };
    }

    public static function userHasRole(?Usuario $user, string $canonicalRole): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(self::roleAliases($canonicalRole));
    }

    /** @param list<string> $canonicalRoles */
    public static function userHasAnyRole(?Usuario $user, array $canonicalRoles): bool
    {
        foreach ($canonicalRoles as $role) {
            if (self::userHasRole($user, $role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Prefijos de permiso con acceso operativo completo por rol (evita 403 si Spatie está desactualizado).
     *
     * @return array<string, list<string>>
     */
    public static function roleOperationalPrefixes(): array
    {
        return [
            'Administrador' => ['admin.', 'voluntarios.', 'voluntario.'],
            'Operador de Incendios' => ['incendios.'],
            'Almacenero' => ['inventario.', 'donante.'],
            'Coordinador Logístico' => ['logistica.'],
            'Coordinador de Voluntarios' => ['voluntarios.'],
            'Jefe de Cuadrilla' => ['cuadrillas.'],
            'Rescatista' => ['rescate.'],
            'Veterinario' => ['veterinaria.'],
            'Cuidador' => ['cuidados.'],
            'Voluntario' => ['voluntario.'],
            'Donante' => ['donante.'],
            'Ciudadano' => ['ciudadano.'],
        ];
    }

    public static function roleGrantsPermission(?Usuario $user, string $permission): bool
    {
        if (! $user) {
            return false;
        }

        foreach (self::FINAL_ROLES as $roleName) {
            if (! self::userHasRole($user, $roleName)) {
                continue;
            }

            $mapped = self::rolePermissionMap()[$roleName] ?? [];
            if (in_array($permission, $mapped, true)) {
                return true;
            }

            foreach (self::roleOperationalPrefixes()[$roleName] ?? [] as $prefix) {
                if (str_starts_with($permission, $prefix)) {
                    return true;
                }
            }
        }

        if (self::userHasRole($user, 'Almacenero') && $permission === 'admin.usuarios.gestionar') {
            return true;
        }

        return false;
    }

    public static function assignCanonicalRole(Usuario $user, string $canonicalRole): void
    {
        foreach (array_merge(self::DEPRECATED_ROLES, self::roleAliases($canonicalRole)) as $alias) {
            if ($alias !== $canonicalRole) {
                $user->removeRole($alias);
            }
        }

        if (! $user->hasRole($canonicalRole)) {
            $user->assignRole($canonicalRole);
        }
    }

    public static function removeCanonicalRole(Usuario $user, string $canonicalRole): void
    {
        foreach (self::roleAliases($canonicalRole) as $alias) {
            $user->removeRole($alias);
        }
    }

    public static function userCan(?Usuario $user, string $permission): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->hasRole('Administrador')) {
            return true;
        }

        if (self::roleGrantsPermission($user, $permission)) {
            return true;
        }

        return $user->can($permission);
    }

    /** @param list<string> $permissions */
    public static function userCanAny(?Usuario $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            $permission = trim($permission);
            if ($permission !== '' && self::userCan($user, $permission)) {
                return true;
            }
        }

        return false;
    }

    public static function syncRolePermissionsIfStale(): void
    {
        static $synced = false;
        if ($synced) {
            return;
        }

        try {
            foreach (self::flattenPermissions() as $permissionName) {
                \Spatie\Permission\Models\Permission::firstOrCreate(
                    ['name' => $permissionName, 'guard_name' => self::GUARD]
                );
            }

            foreach (self::rolePermissionMap() as $roleName => $permissions) {
                $role = \Spatie\Permission\Models\Role::findByName($roleName, self::GUARD);
                $current = $role->permissions->pluck('name')->sort()->values()->all();
                $expected = collect($permissions)->sort()->values()->all();
                if ($current !== $expected) {
                    $role->syncPermissions($permissions);
                }
            }
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Throwable) {
            // La BD puede no estar lista en instalaciones parciales.
        }

        $synced = true;
    }

    public static function canManageRescatePeople(): bool
    {
        return self::userHasRole(auth()->user(), 'Administrador');
    }

    public static function canApproveRescateStaff(): bool
    {
        return self::userHasAnyRole(auth()->user(), ['Administrador', 'Veterinario']);
    }

    public static function canApproveRescateReports(): bool
    {
        return self::userHasAnyRole(auth()->user(), ['Administrador', 'Rescatista', 'Veterinario']);
    }

    public static function canManageRescateReports(): bool
    {
        return self::canApproveRescateReports();
    }

    public static function canDeleteRescateReports(): bool
    {
        return self::userHasRole(auth()->user(), 'Administrador');
    }

    public static function canManageVeterinaryReleases(): bool
    {
        return self::userHasAnyRole(auth()->user(), ['Administrador', 'Veterinario']);
    }

    public static function canOperateIncendios(): bool
    {
        $user = auth()->user();

        if (self::userHasAnyRole($user, ['Administrador', 'Operador de Incendios'])) {
            return true;
        }

        return self::userCanAny($user, [
            'incendios.focos.gestionar',
            'incendios.reportes.ver',
            'incendios.reportes_ciudadanos.ver',
            'incendios.alertas.publicar',
        ]);
    }

    public static function canManageInventarioAlmacenes(?Usuario $user = null): bool
    {
        $user ??= auth()->user();

        return self::userCan($user, 'inventario.almacenes.gestionar');
    }

    public static function canViewInventarioAlmacenes(?Usuario $user = null): bool
    {
        $user ??= auth()->user();

        if (self::canManageInventarioAlmacenes($user)) {
            return true;
        }

        return self::userCan($user, 'comunidad.almacenes.ver');
    }

    public static function canReportIncendios(?Usuario $user = null): bool
    {
        $user ??= auth()->user();

        return self::userCanAny($user, [
            'ciudadano.incendios.reportar',
            'incendios.focos.gestionar',
        ]);
    }

    public static function canViewTerritorialMap(?Usuario $user = null): bool
    {
        $user ??= auth()->user();

        if (self::userHasRole($user, 'Administrador')) {
            return true;
        }

        return self::userCan($user, 'comunidad.mapa_territorial.ver');
    }

    public static function isOnlyRescateCitizen(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return self::userHasRole($user, 'Ciudadano')
            && ! self::userHasAnyRole($user, ['Administrador', 'Rescatista', 'Veterinario', 'Cuidador']);
    }

    public static function redirectPathFor(Usuario $user): string
    {
        if (self::isPublicCommunityUser($user)) {
            return route('incendios.dashboard');
        }

        $role = $user->getRoleNames()->first();

        return match ($role) {
            'Administrador' => route('dashboard'),
            'Operador de Incendios' => route('incendios.dashboard'),
            'Almacenero' => route('inventario.home'),
            'Coordinador Logístico' => route('logistica.dashboard'),
            'Coordinador de Voluntarios' => route('seguimiento.dashboard'),
            'Jefe de Cuadrilla' => route('cuadrillas.dashboard'),
            'Rescatista', 'Veterinario', 'Cuidador' => route('rescate.home'),
            'Voluntario' => route('seguimiento.dashboard'),
            'Donante' => route('inventario.donaciones.index'),
            'Ciudadano' => route('incendios.dashboard'),
            default => route('dashboard'),
        };
    }

    public static function showSidebarModule(?Usuario $user, string $module): bool
    {
        if (! $user) {
            return false;
        }

        if (self::isPublicCommunityUser($user)) {
            return match ($module) {
                'incendios_ciudadano', 'inventario_donante', 'rescate', 'territorial' => true,
                default => false,
            };
        }

        $role = $user->getRoleNames()->first();
        if (! $role) {
            return false;
        }

        if ($role === 'Administrador') {
            return match ($module) {
                'admin', 'inventario', 'incendios', 'logistica', 'seguimiento', 'cuadrillas', 'rescate', 'sync' => true,
                'inventario_donante', 'incendios_ciudadano' => false,
                default => false,
            };
        }

        return match ($module) {
            'admin' => false,
            'inventario' => $role === 'Almacenero',
            'inventario_donante' => $role === 'Donante',
            'incendios' => $role === 'Operador de Incendios',
            'incendios_ciudadano' => $role === 'Ciudadano',
            'logistica' => $role === 'Coordinador Logístico',
            'seguimiento' => in_array($role, ['Coordinador de Voluntarios', 'Voluntario'], true),
            'cuadrillas' => $role === 'Jefe de Cuadrilla',
            'rescate' => in_array($role, ['Rescatista', 'Veterinario', 'Cuidador', 'Ciudadano'], true),
            'sync' => false,
            default => false,
        };
    }

    public static function primaryModuleFor(?Usuario $user): ?string
    {
        if (! $user) {
            return null;
        }

        $role = $user->getRoleNames()->first();
        if ($role === 'Administrador') {
            return 'admin';
        }

        foreach (self::MODULE_PRIMARY_ROLE as $module => $moduleRole) {
            if ($role === $moduleRole) {
                return $module;
            }
        }

        if ($role === 'Veterinario' || $role === 'Cuidador') {
            return 'rescate';
        }

        return null;
    }

    /** Acceso operativo completo al módulo seguimiento (admin + coordinador). */
    public static function gestionSeguimientoCompleta(?Usuario $user): bool
    {
        return $user !== null && self::userHasAnyRole($user, ['Administrador', 'Coordinador de Voluntarios']);
    }

    /** Vista cruzada logística ↔ inventario: solo administrador. */
    public static function vistaIntegradaModulos(?Usuario $user): bool
    {
        return $user !== null && self::userHasRole($user, 'Administrador');
    }

    /** Enfoque de pantallas logísticas: transporte (coordinador) o integrado (admin). */
    public static function enfoqueLogistica(?Usuario $user): string
    {
        return self::vistaIntegradaModulos($user) ? 'integrado' : 'transporte';
    }
}
