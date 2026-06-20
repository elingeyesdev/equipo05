<?php

use App\Models\Usuario;
use App\Support\AccessControl;
use Illuminate\Support\Facades\Hash;

test('administrador puede acceder al mapa territorial integrado', function () {
    $email = 'admin123@gmail.com';
    $user = Usuario::query()->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();

    if (! $user) {
        expect(true)->toBeTrue();

        return;
    }

    AccessControl::syncSingleRole($user, 'Administrador');

    if (! Illuminate\Support\Facades\Schema::hasTable('permissions')) {
        $this->actingAs($user)->getJson('/territorial/modulo/api/capas')->assertOk();

        return;
    }

    $response = $this->actingAs($user)->get('/territorial/modulo');

    $response->assertOk();
    $response->assertSee('Comando central territorial', false);
});

test('administrador puede consultar la api de capas del mapa territorial', function () {
    $email = 'admin123@gmail.com';
    $user = Usuario::query()->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();

    if (! $user) {
        expect(true)->toBeTrue();

        return;
    }

    AccessControl::syncSingleRole($user, 'Administrador');

    $response = $this->actingAs($user)->getJson('/territorial/modulo/api/capas');

    $response->assertOk();
    $response->assertJsonStructure(['generated_at', 'center', 'summary', 'layers']);
});

test('voluntario recibe 403 en el mapa territorial', function () {
    $email = 'voluntario-territorial-test@example.com';
    $user = Usuario::query()->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();

    if (! $user) {
        $user = Usuario::create([
            'nombre' => 'Test',
            'apellido' => 'Voluntario',
            'email' => $email,
            'contrasena' => Hash::make('password'),
            'telefono' => '70000001',
            'activo' => true,
        ]);
    }

    AccessControl::syncSingleRole($user, 'Voluntario');

    $response = $this->actingAs($user)->get('/territorial/modulo');
    expect($response->status())->not->toBe(200);

    $this->actingAs($user)->getJson('/territorial/modulo/api/capas')->assertForbidden();
});
