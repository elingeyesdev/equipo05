<?php

use App\Models\Usuario;
use App\Support\AccessControl;

test('normaliza roles legacy al catalogo canonico del sistema', function () {
    expect(AccessControl::normalizeLegacyRole('admin'))->toBe('Administrador')
        ->and(AccessControl::normalizeLegacyRole('almacenista'))->toBe('Almacenero')
        ->and(AccessControl::normalizeLegacyRole('encargado'))->toBe('Operador de Incendios')
        ->and(AccessControl::normalizeLegacyRole('Reportes'))->toBeNull();
});

test('el administrador puede acceder a todos los modulos operativos', function () {
    $admin = Mockery::mock(Usuario::class);
    $admin->shouldReceive('hasRole')->with('Administrador')->andReturn(true);

    expect(AccessControl::userCanAccessModule($admin, 'cuadrillas'))->toBeTrue()
        ->and(AccessControl::userCanAccessModule($admin, 'seguimiento'))->toBeTrue()
        ->and(AccessControl::userCanAccessModule($admin, 'logistica'))->toBeTrue();
});

test('el jefe de cuadrilla accede al modulo cuadrillas pero no a inventario', function () {
    $jefe = Mockery::mock(Usuario::class);
    $jefe->shouldReceive('hasRole')->with('Administrador')->andReturn(false);
    $jefe->shouldReceive('hasAnyRole')
        ->with(['Jefe de Cuadrilla', 'Operador de Incendios'])
        ->andReturn(true);
    $jefe->shouldReceive('hasAnyRole')
        ->with(['Almacenero', 'Coordinador Logístico', 'Donante'])
        ->andReturn(false);

    expect(AccessControl::userCanAccessModule($jefe, 'cuadrillas'))->toBeTrue()
        ->and(AccessControl::userCanAccessModule($jefe, 'inventario'))->toBeFalse();
});

test('administrador y coordinador tienen gestion completa de seguimiento', function () {
    $admin = Mockery::mock(Usuario::class);
    $admin->shouldReceive('hasAnyRole')
        ->with(['Administrador', 'admin', 'administrador'])
        ->andReturn(true);

    $coordinador = Mockery::mock(Usuario::class);
    $coordinador->shouldReceive('hasAnyRole')
        ->with(['Administrador', 'admin', 'administrador'])
        ->andReturn(false);
    $coordinador->shouldReceive('hasAnyRole')
        ->with(['Coordinador de Voluntarios'])
        ->andReturn(true);

    expect(AccessControl::gestionSeguimientoCompleta($admin))->toBeTrue()
        ->and(AccessControl::gestionSeguimientoCompleta($coordinador))->toBeTrue()
        ->and(AccessControl::gestionSeguimientoCompleta(null))->toBeFalse();
});

test('el enfoque logistico es integrado solo para administrador', function () {
    $admin = Mockery::mock(Usuario::class);
    $admin->shouldReceive('hasAnyRole')
        ->with(['Administrador', 'admin', 'administrador'])
        ->andReturn(true);

    $coordinador = Mockery::mock(Usuario::class);
    $coordinador->shouldReceive('hasAnyRole')
        ->with(['Administrador', 'admin', 'administrador'])
        ->andReturn(false);
    $coordinador->shouldReceive('hasAnyRole')
        ->with(['Coordinador Logístico'])
        ->andReturn(true);

    expect(AccessControl::enfoqueLogistica($admin))->toBe('integrado')
        ->and(AccessControl::enfoqueLogistica($coordinador))->toBe('transporte')
        ->and(AccessControl::vistaIntegradaModulos($admin))->toBeTrue()
        ->and(AccessControl::vistaIntegradaModulos($coordinador))->toBeFalse();
});

test('el mapa de permisos incluye acciones CRUD de inventario', function () {
    $permisos = AccessControl::allPermissionsByModule()['inventario'];

    expect($permisos)->toContain('inventario.donaciones.registrar')
        ->and($permisos)->toContain('inventario.productos.gestionar')
        ->and($permisos)->toContain('inventario.paquetes.gestionar');
});
