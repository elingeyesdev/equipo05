<?php

namespace App\Support;

/**
 * Ubicaciones reales de Santa Cruz y zonas de intervención de fauna silvestre.
 */
final class RescateFieldLocations
{
    /** @var array<int, array{direccion: string, lat: float, lng: float}> */
    private const LOCATIONS = [
        ['direccion' => 'Zona de amortiguación del Urubó, condominio Los Parques', 'lat' => -17.6891, 'lng' => -63.2124],
        ['direccion' => 'Ruta a Cotoca km 14, carretera principal', 'lat' => -17.8042, 'lng' => -63.0117],
        ['direccion' => 'Paurito, ribera del río Piraí', 'lat' => -17.8215, 'lng' => -63.0892],
        ['direccion' => 'Doble vía La Guardia km 18', 'lat' => -17.8912, 'lng' => -63.2456],
        ['direccion' => 'Plan 3000, barrio San Jorge', 'lat' => -17.8123, 'lng' => -63.1234],
        ['direccion' => 'Warnes, plaza principal', 'lat' => -17.5145, 'lng' => -63.1667],
        ['direccion' => 'Buena Vista, camino al Pantanal', 'lat' => -17.4589, 'lng' => -63.6543],
        ['direccion' => 'San José de Chiquitos, plaza principal', 'lat' => -17.8361, 'lng' => -60.7342],
        ['direccion' => 'Roboré, entrada a la Chiquitania', 'lat' => -18.3234, 'lng' => -59.7521],
        ['direccion' => 'Parque Urbano Simón I. Patiño', 'lat' => -17.7833, 'lng' => -63.1821],
        ['direccion' => 'Aeropuerto Viru Viru, zona perimetral este', 'lat' => -17.6486, 'lng' => -63.1405],
        ['direccion' => 'Equipetrol, 4to anillo entre Av. Busch y Beni', 'lat' => -17.7567, 'lng' => -63.1989],
        ['direccion' => 'El Torno, vía a Samaipata km 3', 'lat' => -18.0215, 'lng' => -63.3876],
        ['direccion' => 'Samaipata, camino a la Fortaleza', 'lat' => -18.1794, 'lng' => -63.8755],
        ['direccion' => 'Montero, avenida Grigotá', 'lat' => -17.3389, 'lng' => -63.2508],
        ['direccion' => 'Portachuelo, tramo carretera interdepartamental', 'lat' => -17.3589, 'lng' => -63.3912],
        ['direccion' => 'Valle Sánchez, comunidad Ribera Alta', 'lat' => -17.8234, 'lng' => -63.0567],
        ['direccion' => 'Achocalla, zona ribereña del Piraí', 'lat' => -17.8456, 'lng' => -63.0678],
        ['direccion' => 'Parque Nacional Amboró, ingreso Villa Amboró', 'lat' => -18.1167, 'lng' => -63.5833],
        ['direccion' => 'Concepción, misión jesuita (Chiquitos)', 'lat' => -16.1333, 'lng' => -62.0167],
    ];

    /** @var array<int, array{direccion: string, lat: float, lng: float}> */
    private const RELEASE_AREAS = [
        ['direccion' => 'Reserva municipal Lomas de Arena', 'lat' => -17.9012, 'lng' => -63.3124],
        ['direccion' => 'Parque Lecoq, zona de reintroducción', 'lat' => -17.7689, 'lng' => -63.1543],
        ['direccion' => 'Área protegida Río Piraí norte', 'lat' => -17.7345, 'lng' => -63.0876],
        ['direccion' => 'Bosque secundario Warnes', 'lat' => -17.4987, 'lng' => -63.1789],
        ['direccion' => 'Chiquitania, zona de amortiguación Roboré', 'lat' => -18.3012, 'lng' => -59.8012],
        ['direccion' => 'Samaipata, quebrada de reintroducción', 'lat' => -18.1923, 'lng' => -63.8901],
    ];

    /**
     * @return array{direccion: string, lat: float, lng: float}
     */
    public static function get(int $index): array
    {
        $locations = self::LOCATIONS;
        $item = $locations[$index % count($locations)];

        return [
            'direccion' => $item['direccion'],
            'lat' => $item['lat'],
            'lng' => $item['lng'],
        ];
    }

    /**
     * @return array{direccion: string, lat: float, lng: float}
     */
    public static function releaseArea(int $index): array
    {
        $areas = self::RELEASE_AREAS;
        $item = $areas[$index % count($areas)];

        return [
            'direccion' => $item['direccion'],
            'lat' => $item['lat'],
            'lng' => $item['lng'],
        ];
    }

    public static function count(): int
    {
        return count(self::LOCATIONS);
    }

    public static function looksLikeDemoAddress(?string $direccion): bool
    {
        if ($direccion === null || $direccion === '') {
            return true;
        }

        $text = mb_strtolower($direccion, 'UTF-8');

        return str_contains($text, 'demo')
            || str_contains($text, 'av. demo')
            || str_contains($text, 'reintroducción demo');
    }

    /**
     * Restaura coordenadas a partir de palabras clave en la dirección (hallazgos showcase).
     *
     * @return array{direccion: string, lat: float, lng: float}|null
     */
    public static function matchByDireccion(?string $direccion): ?array
    {
        if ($direccion === null || $direccion === '') {
            return null;
        }

        $text = self::normalize($direccion);
        $keywords = [
            'urubó' => 0,
            'urubo' => 0,
            'cotoca' => 1,
            'paurito' => 2,
            'la guardia' => 3,
            'plan 3000' => 4,
            'warnes' => 5,
            'buena vista' => 6,
            'san josé de chiquitos' => 7,
            'san jose de chiquitos' => 7,
            'roboré' => 8,
            'robore' => 8,
            'patiño' => 9,
            'simon i' => 9,
            'viru viru' => 10,
            'equipetrol' => 11,
            'el torno' => 12,
            'samaipata' => 13,
            'montero' => 14,
            'portachuelo' => 15,
            'valle sánchez' => 16,
            'valle sanchez' => 16,
            'achocalla' => 17,
            'amboró' => 18,
            'amboro' => 18,
            'concepción' => 19,
            'concepcion' => 19,
        ];

        foreach ($keywords as $keyword => $index) {
            if (str_contains($text, $keyword)) {
                return self::get($index);
            }
        }

        return null;
    }

    private static function normalize(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $text);

        return $text;
    }
}
