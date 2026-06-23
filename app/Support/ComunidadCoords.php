<?php

namespace App\Support;

class ComunidadCoords
{
    /** @var array<string, array{lat: float, lng: float}> */
    private const COORDS = [
        'Warnes' => ['lat' => -17.5167, 'lng' => -63.1667],
        'San Ignacio de Velasco' => ['lat' => -16.3667, 'lng' => -60.9500],
        'Montero' => ['lat' => -17.3378, 'lng' => -63.2500],
        'El Torno' => ['lat' => -17.9833, 'lng' => -63.3833],
        'Cotoca' => ['lat' => -17.7544, 'lng' => -62.9336],
        'Portachuelo' => ['lat' => -17.8833, 'lng' => -63.2167],
        'San Matías' => ['lat' => -19.6519, 'lng' => -57.6333],
        'Pailón' => ['lat' => -18.0167, 'lng' => -63.3167],
        'Cuatro Cañadas' => ['lat' => -17.4500, 'lng' => -63.8500],
        'Mineros' => ['lat' => -17.5500, 'lng' => -63.9000],
        'Puerto Suárez' => ['lat' => -18.3167, 'lng' => -57.7333],
        'Roboré' => ['lat' => -18.3333, 'lng' => -59.7500],
        'Concepción' => ['lat' => -16.4333, 'lng' => -62.0167],
        'San Javier' => ['lat' => -16.2667, 'lng' => -62.1333],
        'Ascensión de Guarayos' => ['lat' => -15.7167, 'lng' => -62.9833],
        'San Julián' => ['lat' => -17.7833, 'lng' => -60.1000],
        'Charagua' => ['lat' => -19.7833, 'lng' => -63.2000],
        'Yapacaní' => ['lat' => -17.4000, 'lng' => -63.8833],
        'Buena Vista' => ['lat' => -17.4667, 'lng' => -63.9833],
        'Limoncito' => ['lat' => -17.9500, 'lng' => -63.4500],
    ];

    /** @return array{lat: float, lng: float}|null */
    public static function resolver(?string $comunidad): ?array
    {
        if ($comunidad === null || trim($comunidad) === '') {
            return null;
        }

        $key = trim($comunidad);
        if (isset(self::COORDS[$key])) {
            return self::COORDS[$key];
        }

        $lower = mb_strtolower($key);
        foreach (self::COORDS as $nombre => $coords) {
            if (mb_strtolower($nombre) === $lower) {
                return $coords;
            }
        }

        foreach (self::COORDS as $nombre => $coords) {
            if (str_contains($lower, mb_strtolower($nombre))
                || str_contains(mb_strtolower($nombre), $lower)) {
                return $coords;
            }
        }

        return null;
    }
}
