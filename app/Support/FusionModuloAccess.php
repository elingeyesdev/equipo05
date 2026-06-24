<?php

namespace App\Support;

use App\Models\Usuario;

/**
 * Permisos y restricciones para módulos fusionados (logística, seguimiento, cuadrillas).
 */
class FusionModuloAccess
{
    public const LOGISTICA_PERMISSIONS = 'logistica.dashboard.ver|logistica.solicitudes.gestionar|logistica.vehiculos.gestionar|logistica.conductores.gestionar|logistica.destinos.gestionar|logistica.entregas.gestionar|logistica.seguimiento.gestionar|logistica.reportes.ver';

    public const VOLUNTARIOS_PERMISSIONS = 'voluntarios.dashboard.ver|voluntarios.gestionar|voluntarios.evaluaciones.gestionar|voluntarios.capacitaciones.gestionar|voluntarios.necesidades.gestionar|voluntarios.asignaciones.gestionar|voluntarios.reportes.ver';

    public const VOLUNTARIO_PANEL_PERMISSIONS = 'voluntario.panel.ver|voluntario.tareas.ver|voluntario.capacitaciones.ver|voluntario.participacion.registrar';

    public const CUADRILLAS_PERMISSIONS = 'cuadrillas.dashboard.ver|cuadrillas.equipos.gestionar|cuadrillas.recursos.gestionar|cuadrillas.reportes_campo.gestionar|cuadrillas.cursos.gestionar|cuadrillas.kardex.gestionar|cuadrillas.reportes.ver';

    public const INCENDIOS_CITIZEN_PERMISSIONS = 'incendios.dashboard.ver|ciudadano.alertas.ver|ciudadano.incendios.reportar|ciudadano.reportes_propios.ver';

    public const INCENDIOS_OPERATE_PERMISSIONS = 'incendios.alertas.publicar|incendios.reportes_ciudadanos.ver|incendios.reportes_ciudadanos.validar|incendios.focos.gestionar|incendios.estados.actualizar|incendios.reportes.ver';

    public const INCENDIOS_PERMISSIONS = self::INCENDIOS_CITIZEN_PERMISSIONS.'|'.self::INCENDIOS_OPERATE_PERMISSIONS;

    public const RESCATE_PERMISSIONS = 'rescate.dashboard.ver|rescate.reportes.ver|rescate.rescates.gestionar|rescate.traslados.gestionar|rescate.animales.ver|veterinaria.dashboard.ver|veterinaria.animales.ver|veterinaria.evaluaciones.gestionar|veterinaria.diagnosticos.gestionar|veterinaria.tratamientos.gestionar|veterinaria.liberaciones.gestionar|veterinaria.reportes.ver|cuidados.dashboard.ver|cuidados.animales.ver|cuidados.alimentacion.registrar|cuidados.cuidados_diarios.registrar|cuidados.observaciones.registrar|ciudadano.animales.reportar|ciudadano.reportes_propios.ver';

    /** @var list<string> Subsecciones del módulo seguimiento expuestas en rutas y navegación. */
    public const SEGUIMIENTO_SECTIONS = [
        'voluntarios',
        'voluntarios-inactivos',
        'evaluacion',
        'evaluacion-pruebas',
        'capacitaciones',
        'necesidades',
        'ayudas-solicitadas',
        'administradores',
        'universidades',
        'chat-consulta',
        'helpdesk',
    ];

    /** @var list<string> Secciones donde el voluntario puede crear/editar (participación operativa). */
    private const VOLUNTARIO_WRITE_SECTIONS = [
        'ayudas-solicitadas',
        'chat-consulta',
        'evaluacion-pruebas',
        'helpdesk',
    ];

    public static function assertModuleAccess(string $module): void
    {
        $permissions = match ($module) {
            'logistica' => explode('|', self::LOGISTICA_PERMISSIONS),
            'seguimiento' => array_merge(
                explode('|', self::VOLUNTARIOS_PERMISSIONS),
                explode('|', self::VOLUNTARIO_PANEL_PERMISSIONS),
            ),
            'cuadrillas' => explode('|', self::CUADRILLAS_PERMISSIONS),
            default => [],
        };

        abort_unless(AccessControl::userCanAny(auth()->user(), $permissions), 403);
    }

    public static function assertCanWriteModule(string $module): void
    {
        $user = auth()->user();
        if (AccessControl::userHasRole($user, 'Administrador')) {
            return;
        }

        if ($module === 'seguimiento' && AccessControl::userHasRole($user, 'Coordinador de Voluntarios')) {
            return;
        }

        $writePermissions = match ($module) {
            'logistica' => [
                'logistica.solicitudes.gestionar',
                'logistica.vehiculos.gestionar',
                'logistica.conductores.gestionar',
                'logistica.destinos.gestionar',
                'logistica.entregas.gestionar',
                'logistica.seguimiento.gestionar',
            ],
            'seguimiento' => [
                'voluntarios.gestionar',
                'voluntarios.evaluaciones.gestionar',
                'voluntarios.capacitaciones.gestionar',
                'voluntarios.necesidades.gestionar',
                'voluntarios.asignaciones.gestionar',
            ],
            'cuadrillas' => [
                'cuadrillas.equipos.gestionar',
                'cuadrillas.recursos.gestionar',
                'cuadrillas.reportes_campo.gestionar',
                'cuadrillas.cursos.gestionar',
                'cuadrillas.kardex.gestionar',
            ],
            default => [],
        };

        abort_unless(AccessControl::userCanAny($user, $writePermissions), 403);
    }

    public static function assertSeguimientoSection(string $seccion, bool $escritura = false): void
    {
        $user = auth()->user();

        if (AccessControl::userHasAnyRole($user, ['Administrador', 'Coordinador de Voluntarios'])) {
            return;
        }

        if (AccessControl::userHasRole($user, 'Voluntario')) {
            if ($escritura) {
                abort_unless(in_array($seccion, self::VOLUNTARIO_WRITE_SECTIONS, true), 403);

                return;
            }

            abort_unless(in_array($seccion, self::SEGUIMIENTO_SECTIONS, true), 403);

            return;
        }

        abort_unless(AccessControl::userCan($user, 'voluntarios.dashboard.ver'), 403);
    }

    /** Indica si el usuario autenticado puede crear/editar registros en una subsección de seguimiento. */
    public static function canWriteSeguimientoSection(string $seccion): bool
    {
        $user = auth()->user();

        if (AccessControl::gestionSeguimientoCompleta($user)) {
            return true;
        }

        if (AccessControl::userHasRole($user, 'Voluntario')) {
            return in_array($seccion, self::VOLUNTARIO_WRITE_SECTIONS, true);
        }

        $writePermissions = [
            'voluntarios.gestionar',
            'voluntarios.evaluaciones.gestionar',
            'voluntarios.capacitaciones.gestionar',
            'voluntarios.necesidades.gestionar',
            'voluntarios.asignaciones.gestionar',
        ];

        return AccessControl::userCanAny($user, $writePermissions);
    }

    public static function assertLogisticaWrite(?Usuario $user = null): void
    {
        $user ??= auth()->user();

        if (AccessControl::userHasRole($user, 'Administrador')) {
            return;
        }

        abort_unless(
            AccessControl::userCanAny($user, [
                'logistica.solicitudes.gestionar',
                'logistica.vehiculos.gestionar',
                'logistica.conductores.gestionar',
                'logistica.destinos.gestionar',
                'logistica.entregas.gestionar',
                'logistica.seguimiento.gestionar',
            ]),
            403
        );
    }

    public static function assertGatewayLogistica(?Usuario $user = null): void
    {
        $user ??= auth()->user();

        abort_unless(
            AccessControl::userCanAny($user, [
                'logistica.solicitudes.gestionar',
                'logistica.seguimiento.gestionar',
                'inventario.paquetes.gestionar',
                'inventario.paquetes.ver',
            ]),
            403
        );
    }
}
