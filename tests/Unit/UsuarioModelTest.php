<?php

use App\Models\Usuario;

uses(Tests\TestCase::class);

test('normaliza el email a minusculas al asignarlo al usuario', function () {
    $usuario = new Usuario;
    $usuario->email = '  Admin123@GMAIL.COM  ';

    expect($usuario->email)->toBe('admin123@gmail.com');
});
