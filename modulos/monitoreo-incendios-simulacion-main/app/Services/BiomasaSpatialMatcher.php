<?php

namespace Modules\Incendios\Services;

use Illuminate\Support\Collection;
use Modules\Incendios\Models\Biomasa;
use Modules\Incendios\Models\FocoIncendio;

class BiomasaSpatialMatcher
{
    /**
     * Asocia cada foco con la biomasa aprobada que contiene sus coordenadas
     * (o la relación directa biomasa_id si existe).
     */
    public function enrichFocos(Collection $fires, ?Collection $biomasas = null): Collection
    {
        $biomasas = $biomasas ?? Biomasa::aprobadas()->with('tipoBiomasa')->get();
        $polygons = $this->buildPolygonIndex($biomasas);

        return $fires->map(function (FocoIncendio $fire) use ($polygons) {
            if ($fire->relationLoaded('biomasa') && $fire->biomasa) {
                return $fire;
            }

            $lat = $fire->latitude;
            $lng = $fire->longitude;

            if ($lat === null || $lng === null) {
                return $fire;
            }

            foreach ($polygons as $item) {
                if (self::pointInPolygon($lat, $lng, $item['points'])) {
                    $fire->setRelation('biomasa', $item['biomasa']);

                    return $fire;
                }
            }

            return $fire;
        });
    }

    /**
     * @return array<int, array{biomasa: Biomasa, points: array<int, array{0: float, 1: float}>}>
     */
    private function buildPolygonIndex(Collection $biomasas): array
    {
        $index = [];

        foreach ($biomasas as $biomasa) {
            $points = $this->normalizePolygon($biomasa->coordenadas ?? []);
            if (count($points) >= 3) {
                $index[] = ['biomasa' => $biomasa, 'points' => $points];
            }
        }

        return $index;
    }

    /**
     * @param  array<int, mixed>  $coords
     * @return array<int, array{0: float, 1: float}>
     */
    private function normalizePolygon(array $coords): array
    {
        $points = [];

        foreach ($coords as $c) {
            if (! is_array($c)) {
                continue;
            }

            $lat = $c['lat'] ?? $c['latitude'] ?? $c[0] ?? null;
            $lng = $c['lng'] ?? $c['lon'] ?? $c['longitude'] ?? $c[1] ?? null;

            if ($lat !== null && $lng !== null) {
                $points[] = [(float) $lat, (float) $lng];
            }
        }

        return $points;
    }

    /**
     * Ray-casting: punto dentro de polígono (lat = y, lng = x).
     *
     * @param  array<int, array{0: float, 1: float}>  $polygon
     */
    public static function pointInPolygon(float $lat, float $lng, array $polygon): bool
    {
        $n = count($polygon);
        if ($n < 3) {
            return false;
        }

        $inside = false;

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $yi = $polygon[$i][0];
            $xi = $polygon[$i][1];
            $yj = $polygon[$j][0];
            $xj = $polygon[$j][1];

            $intersect = (($yi > $lat) !== ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / (($yj - $yi) ?: 1e-12) + $xi);

            if ($intersect) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }
}
