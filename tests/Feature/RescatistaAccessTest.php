<?php

use App\Models\Usuario;
use App\Support\AccessControl;
use Illuminate\Support\Facades\Hash;

test('rescatista can access rescate home without redirect loop', function () {
    $email = 'rescatista-loop-test@example.com';

    $user = Usuario::query()->whereRaw('LOWER(email) = ?', [strtolower($email)])->first();
    if (! $user) {
        $user = Usuario::create([
            'nombre' => 'Test',
            'apellido' => 'Rescatista',
            'email' => $email,
            'contrasena' => Hash::make('password'),
            'telefono' => '70000000',
            'activo' => true,
        ]);
    }

    AccessControl::syncSingleRole($user, 'Rescatista');

    $response = $this->actingAs($user)->get('/rescate/modulo/home');

    expect($response->status())->not->toBe(302)
        ->and(AccessControl::userCanAccessModule($user, 'rescate'))->toBeTrue();
});
