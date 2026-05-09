<?php

namespace Modules\Incendios\Support;

/**
 * Ubicaciones de referencia para consultas Open-Meteo (dashboard y datos históricos).
 */
final class ClimaUbicaciones
{
    private const DEFAULT_KEY = 'san-jose';

    /**
     * @return array<string, array{nombre: string, lat: float, lng: float}>
     */
    public static function all(): array
    {
        return [
            'san-jose' => [
                'nombre' => 'San José de Chiquitos',
                'lat' => -17.8857,
                'lng' => -60.7556,
            ],
            'santa-cruz-sierra' => [
                'nombre' => 'Santa Cruz de la Sierra',
                'lat' => -17.7833,
                'lng' => -63.1821,
            ],
            'robore' => [
                'nombre' => 'Roboré',
                'lat' => -18.3333,
                'lng' => -59.7667,
            ],
            'san-ignacio' => [
                'nombre' => 'San Ignacio de Velasco',
                'lat' => -16.3667,
                'lng' => -60.9500,
            ],
            'concepcion' => [
                'nombre' => 'Concepción',
                'lat' => -16.1500,
                'lng' => -62.0333,
            ],
            'san-matias' => [
                'nombre' => 'San Matías',
                'lat' => -16.3500,
                'lng' => -58.4000,
            ],
            'san-rafael' => [
                'nombre' => 'San Rafael de Velasco',
                'lat' => -16.7833,
                'lng' => -60.6833,
            ],
            'san-miguel' => [
                'nombre' => 'San Miguel de Velasco',
                'lat' => -16.6833,
                'lng' => -60.9667,
            ],
            'santa-ana' => [
                'nombre' => 'Santa Ana de Velasco',
                'lat' => -16.6667,
                'lng' => -60.7167,
            ],
            'santiago' => [
                'nombre' => 'Santiago de Chiquitos',
                'lat' => -18.3333,
                'lng' => -59.6000,
            ],
            'chochis' => [
                'nombre' => 'Chochís',
                'lat' => -18.2167,
                'lng' => -59.7000,
            ],
            'aguas-calientes' => [
                'nombre' => 'Aguas Calientes',
                'lat' => -18.0500,
                'lng' => -59.6167,
            ],
            'quimome' => [
                'nombre' => 'Quimome',
                'lat' => -17.5000,
                'lng' => -60.4833,
            ],
        ];
    }

    public static function defaultKey(): string
    {
        return self::DEFAULT_KEY;
    }

    public static function normalizeKey(string|int|null $key): string
    {
        $key = $key === null || $key === '' ? null : (string) $key;
        $all = self::all();
        if ($key !== null && isset($all[$key])) {
            return $key;
        }

        return self::DEFAULT_KEY;
    }
}
