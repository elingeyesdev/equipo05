<?php

use App\Models\Usuario;
use App\Support\FusionModuloAccess;
use Illuminate\Support\Facades\Auth;

function mockVoluntarioUser(): Usuario
{
    $voluntario = Mockery::mock(Usuario::class);
    $voluntario->shouldReceive('hasAnyRole')->andReturnUsing(function (array $roles): bool {
        $aliases = ['Voluntario', 'voluntario'];

        return (bool) array_intersect($aliases, $roles);
    });
    $voluntario->shouldReceive('hasRole')->andReturnUsing(function (string $role): bool {
        return in_array($role, ['Voluntario', 'voluntario'], true);
    });

    return $voluntario;
}

function mockCoordinadorVoluntariosUser(): Usuario
{
    $coordinador = Mockery::mock(Usuario::class);
    $coordinador->shouldReceive('hasAnyRole')->andReturnUsing(function (array $roles): bool {
        $aliases = ['Coordinador de Voluntarios', 'Administrador', 'admin', 'administrador'];

        return (bool) array_intersect($aliases, $roles);
    });
    $coordinador->shouldReceive('hasRole')->andReturn(false);

    return $coordinador;
}

test('el rol voluntario puede leer todas las subsecciones del modulo seguimiento', function () {
    Auth::shouldReceive('user')->andReturn(mockVoluntarioUser());

    foreach (FusionModuloAccess::SEGUIMIENTO_SECTIONS as $seccion) {
        FusionModuloAccess::assertSeguimientoSection($seccion);
        expect(true)->toBeTrue();
    }
});

test('el rol voluntario no puede escribir en secciones administrativas de seguimiento', function () {
    Auth::shouldReceive('user')->andReturn(mockVoluntarioUser());

    expect(fn () => FusionModuloAccess::assertSeguimientoSection('administradores', true))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);

    expect(fn () => FusionModuloAccess::assertSeguimientoSection('voluntarios', true))
        ->toThrow(\Symfony\Component\HttpKernel\Exception\HttpException::class);
});

test('el rol voluntario puede escribir en ayudas chat y helpdesk', function () {
    Auth::shouldReceive('user')->andReturn(mockVoluntarioUser());

    foreach (['ayudas-solicitadas', 'chat-consulta', 'evaluacion-pruebas', 'helpdesk'] as $seccion) {
        FusionModuloAccess::assertSeguimientoSection($seccion, true);
        expect(FusionModuloAccess::canWriteSeguimientoSection($seccion))->toBeTrue();
    }
});

test('evaluacion era una seccion bloqueada para voluntario y ahora es legible', function () {
    Auth::shouldReceive('user')->andReturn(mockVoluntarioUser());

    FusionModuloAccess::assertSeguimientoSection('evaluacion');
    expect(FusionModuloAccess::canWriteSeguimientoSection('evaluacion'))->toBeFalse();
});

test('coordinador de voluntarios mantiene acceso completo a seguimiento', function () {
    Auth::shouldReceive('user')->andReturn(mockCoordinadorVoluntariosUser());

    FusionModuloAccess::assertSeguimientoSection('evaluacion', true);
    FusionModuloAccess::assertSeguimientoSection('administradores', true);
    expect(FusionModuloAccess::canWriteSeguimientoSection('administradores'))->toBeTrue();
});
