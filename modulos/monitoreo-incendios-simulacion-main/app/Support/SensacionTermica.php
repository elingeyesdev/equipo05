<?php

namespace Modules\Incendios\Support;

/**
 * Sensación térmica ajustada para clima tropical (Santa Cruz / Chiquitanía).
 * Combina temperatura aparente (Steadman), viento, ráfagas, humedad, lluvia y UV.
 */
final class SensacionTermica
{
    public static function calcular(
        float $temperaturaC,
        float $humedadPct,
        float $vientoKmh,
        float $rafagasKmh = 0,
        float $precipitacionMm = 0,
        ?float $uvIndex = null,
        ?float $aparenteOpenMeteo = null
    ): float {
        $base = $aparenteOpenMeteo ?? self::temperaturaAparenteSteadman($temperaturaC, $humedadPct, $vientoKmh);

        $vientoEfectivo = max($vientoKmh, $rafagasKmh * 0.85);

        if ($temperaturaC <= 22) {
            $penalHumedad = max(0, ($humedadPct - 50) * 0.14);
            $penalLluvia = $precipitacionMm > 0 ? min(4.0, $precipitacionMm * 0.6) : 0;
            $penalViento = $vientoEfectivo >= 12 ? min(3.0, ($vientoEfectivo - 10) * 0.15) : 0;
            $base -= $penalHumedad + $penalLluvia + $penalViento;

            if ($temperaturaC <= 16 && $humedadPct >= 60) {
                $base -= 2.5;
            }
        } elseif ($temperaturaC >= 28) {
            if ($uvIndex !== null && $uvIndex >= 5) {
                $base += min(3.0, ($uvIndex - 4) * 0.4);
            }
            if ($humedadPct >= 70) {
                $base += min(4.0, ($humedadPct - 65) * 0.12);
            }
        }

        return round(max(-15.0, min(55.0, $base)), 1);
    }

    /**
     * @param  array<int, float|int|null>  $temperaturas
     * @param  array<int, float|int|null>  $humedades
     * @param  array<int, float|int|null>  $vientos
     * @return array<int, float>
     */
    public static function serie(
        array $temperaturas,
        array $humedades,
        array $vientos,
        array $rafagas = [],
        array $precipitaciones = [],
        array $aparentes = []
    ): array {
        $out = [];
        $ultimaTemp = null;
        foreach ($temperaturas as $i => $temp) {
            if ($temp !== null) {
                $ultimaTemp = (float) $temp;
            }
            if ($ultimaTemp === null) {
                $out[] = null;

                continue;
            }
            $out[] = self::calcular(
                (float) $ultimaTemp,
                (float) ($humedades[$i] ?? 50),
                (float) ($vientos[$i] ?? 0),
                (float) ($rafagas[$i] ?? 0),
                (float) ($precipitaciones[$i] ?? 0),
                null,
                isset($aparentes[$i]) ? (float) $aparentes[$i] : null
            );
        }

        return $out;
    }

    private static function temperaturaAparenteSteadman(float $t, float $rh, float $v): float
    {
        $v = max(0.1, $v);
        $e = ($rh / 100) * 6.105 * exp((17.27 * $t) / (237.7 + $t));

        return $t + 0.33 * $e - 0.70 * $v - 4.00;
    }
}
