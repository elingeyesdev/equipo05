<?php

use App\Support\LogisticaOperativa;

test('genera referencia legible para solicitudes logisticas', function () {
    expect(LogisticaOperativa::refSolicitud(7))->toBe('#0007')
        ->and(LogisticaOperativa::refSolicitud(120))->toBe('#0120');
});

test('genera referencia legible para paquetes logisticos', function () {
    expect(LogisticaOperativa::refPaquete(3))->toBe('#0003')
        ->and(LogisticaOperativa::refPaquete(9999))->toBe('#9999');
});

test('presenta solicitud pendiente con etiquetas para la vista operativa', function () {
    $row = (object) [
        'id_solicitud' => 15,
        'codigo_seguimiento' => 'LOG-DEMO-015',
        'estado' => 'pendiente',
        'aprobada' => false,
        'tipo_emergencia' => 'Incendio forestal',
        'cantidad_personas' => 12,
        'fecha_inicio' => null,
        'fecha_necesidad' => null,
        'fecha_solicitud' => '2026-06-10 08:30:00',
        'created_at' => '2026-06-10 08:30:00',
        'insumos_necesarios' => 'Agua, mochilas y EPP',
        'solicitante_nombre' => 'María',
        'solicitante_apellido' => 'Ríos',
        'solicitante_ci' => '4567890',
        'solicitante_telefono' => '71234567',
        'destino_comunidad' => 'San Ignacio',
        'destino_provincia' => 'Chiquitos',
        'destino_direccion' => 'Comunidad central',
        'paquete_logistica_id' => null,
        'paquete_logistica_codigo' => null,
        'paquete_estado' => null,
    ];

    $presentacion = LogisticaOperativa::presentarSolicitud($row, 'transporte');

    expect($presentacion['ref'])->toBe('#0015')
        ->and($presentacion['estado_filtro'])->toBe('pendiente')
        ->and($presentacion['estado_badge'])->toBe('warning')
        ->and($presentacion['solicitante_nombre'])->toBe('María Ríos')
        ->and($presentacion['vista_integrada'])->toBeFalse();
});

test('clasifica solicitud aprobada con badge de exito', function () {
    $row = (object) [
        'id_solicitud' => 2,
        'codigo_seguimiento' => 'LOG-002',
        'estado' => 'aprobada',
        'aprobada' => true,
        'tipo_emergencia' => 'Inundación',
        'cantidad_personas' => 5,
        'fecha_solicitud' => '2026-06-11 10:00:00',
        'created_at' => '2026-06-11 10:00:00',
        'insumos_necesarios' => 'Colchones',
        'solicitante_nombre' => 'Juan',
        'solicitante_apellido' => 'Pérez',
        'solicitante_ci' => '1234567',
        'solicitante_telefono' => '79876543',
        'destino_comunidad' => 'Concepción',
        'destino_provincia' => 'Chiquitos',
        'destino_direccion' => 'Zona norte',
        'paquete_logistica_id' => 8,
        'paquete_logistica_codigo' => 'PKG-008',
        'paquete_estado' => 'en transito',
    ];

    $presentacion = LogisticaOperativa::presentarSolicitud($row, 'transporte');

    expect($presentacion['estado_filtro'])->toBe('aprobada')
        ->and($presentacion['estado_badge'])->toBe('success')
        ->and($presentacion['aprobada'])->toBeTrue();
});
