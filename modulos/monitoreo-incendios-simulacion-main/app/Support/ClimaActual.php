<?php

namespace Modules\Incendios\Support;

/**
 * Normaliza lecturas actuales de Open-Meteo (bloque current, current_weather u hourly).
 */
final class ClimaActual
{
    /**
     * @return array{
     *   temperatura: float|null,
     *   humedad: float|null,
     *   viento: float|null,
     *   rafagas: float|null,
     *   precipitacion: float|null,
     *   sensacion_termica: float|null,
     *   uv_index: float|null
     * }|null
     */
    public static function fromOpenMeteo(?array $data): ?array
    {
        if ($data === null || $data === []) {
            return null;
        }

        $current = $data['current'] ?? null;
        $legacy = $data['current_weather'] ?? null;
        $hourly = $data['hourly'] ?? [];
        $idx = self::indiceHorarioActual($hourly);

        $temperatura = self::primeroNoNulo([
            $current['temperature_2m'] ?? null,
            $legacy['temperature'] ?? null,
            $hourly['temperature_2m'][$idx] ?? null,
        ]);

        $humedad = self::primeroNoNulo([
            $current['relative_humidity_2m'] ?? null,
            $hourly['relative_humidity_2m'][$idx] ?? null,
        ]);

        $viento = self::primeroNoNulo([
            $current['wind_speed_10m'] ?? null,
            $legacy['windspeed'] ?? null,
            $hourly['wind_speed_10m'][$idx] ?? null,
        ]);

        $rafagas = self::primeroNoNulo([
            $current['wind_gusts_10m'] ?? null,
            $hourly['wind_gusts_10m'][$idx] ?? null,
        ]);

        if ($rafagas === null && $viento !== null) {
            $rafagas = round((float) $viento * 1.35, 1);
        }

        $precipitacion = self::primeroNoNulo([
            $current['precipitation'] ?? null,
            $hourly['precipitation'][$idx] ?? null,
        ]);

        $uvIndex = self::primeroNoNulo([
            $current['uv_index'] ?? null,
            $hourly['uv_index'][$idx] ?? null,
        ]);

        $aparente = self::primeroNoNulo([
            $current['apparent_temperature'] ?? null,
            $hourly['apparent_temperature'][$idx] ?? null,
        ]);

        if ($temperatura === null) {
            return null;
        }

        $sensacion = SensacionTermica::calcular(
            (float) $temperatura,
            (float) ($humedad ?? 50),
            (float) ($viento ?? 0),
            (float) ($rafagas ?? 0),
            (float) ($precipitacion ?? 0),
            $uvIndex !== null ? (float) $uvIndex : null,
            $aparente !== null ? (float) $aparente : null
        );

        return [
            'temperatura' => round((float) $temperatura, 1),
            'humedad' => $humedad !== null ? round((float) $humedad, 0) : null,
            'viento' => $viento !== null ? round((float) $viento, 1) : null,
            'rafagas' => $rafagas !== null ? round((float) $rafagas, 1) : null,
            'precipitacion' => $precipitacion !== null ? round((float) $precipitacion, 1) : null,
            'sensacion_termica' => $sensacion,
            'uv_index' => $uvIndex !== null ? round((float) $uvIndex, 1) : null,
        ];
    }

    /**
     * @param  array<string, mixed>  $hourly
     */
    private static function indiceHorarioActual(array $hourly): int
    {
        $times = $hourly['time'] ?? [];
        if (! is_array($times) || $times === []) {
            return 0;
        }

        $now = time();
        $mejor = 0;
        $mejorDiff = PHP_INT_MAX;

        foreach ($times as $i => $iso) {
            $ts = strtotime((string) $iso);
            if ($ts === false) {
                continue;
            }
            $diff = abs($ts - $now);
            if ($diff < $mejorDiff) {
                $mejorDiff = $diff;
                $mejor = (int) $i;
            }
        }

        return $mejor;
    }

    /**
     * @param  array<int, mixed>  $valores
     */
    private static function primeroNoNulo(array $valores): ?float
    {
        foreach ($valores as $v) {
            if ($v !== null && $v !== '') {
                return (float) $v;
            }
        }

        return null;
    }
}
