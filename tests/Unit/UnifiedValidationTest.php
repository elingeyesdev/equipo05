<?php

use App\Support\UnifiedValidation;

test('divide nombre completo en nombre y apellido para registros unificados', function () {
    $resultado = UnifiedValidation::splitNombreCompleto('Ana María López');

    expect($resultado['nombre'])->toBe('Ana')
        ->and($resultado['apellido'])->toBe('María López');
});

test('asigna valores por defecto cuando el nombre completo esta vacio', function () {
    $resultado = UnifiedValidation::splitNombreCompleto('   ');

    expect($resultado['nombre'])->toBe('Usuario')
        ->and($resultado['apellido'])->toBe('-');
});
