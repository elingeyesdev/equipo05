<?php

namespace Modules\Incendios\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prediction extends Model
{
    protected $connection = 'incendios';
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'foco_incendio_id',
        'predicted_at',
        'path',
        'meta',
        'user_id',
        'ci_usuario',
    ];

    protected $casts = [
        'predicted_at' => 'datetime',
        'path' => 'array',
        'meta' => 'array',
    ];

    /**
     * Foco de incendio al que pertenece esta predicción
     */
    public function focoIncendio()
    {
        return $this->belongsTo(\Modules\Incendios\Models\FocosIncendio::class, 'foco_incendio_id');
    }

    /**
     * Trayectoria unificada para vistas, PDF y mapa (formato actual + legacy del seeder).
     *
     * @return array<int, array<string, mixed>>
     */
    public function normalizedTrajectory(): array
    {
        $raw = $this->meta['trajectory'] ?? $this->path ?? [];

        if (! is_array($raw)) {
            return [];
        }

        $normalized = [];

        foreach ($raw as $index => $point) {
            if (! is_array($point)) {
                continue;
            }

            $lat = $point['lat'] ?? $point['latitude'] ?? (is_numeric($point[0] ?? null) ? $point[0] : null);
            $lng = $point['lng'] ?? $point['lon'] ?? $point['longitude'] ?? (is_numeric($point[1] ?? null) ? $point[1] : null);

            if ($lat === null || $lng === null) {
                continue;
            }

            $hour = $point['hour'] ?? $point['time'] ?? $point['step'] ?? $index;
            $intensity = $point['intensity'] ?? $point['intensidad'] ?? 5;
            $spread = $point['spread_radius_km'] ?? $point['radius_km'] ?? 0.5;
            $area = $point['affected_area_km2'] ?? $point['area_km2'] ?? 0;
            $perimeter = $point['perimeter_km'] ?? (2 * M_PI * (float) $spread);

            $normalized[] = array_merge($point, [
                'hour' => (int) $hour,
                'lat' => (float) $lat,
                'lng' => (float) $lng,
                'intensity' => (float) $intensity,
                'spread_radius_km' => (float) $spread,
                'affected_area_km2' => (float) $area,
                'perimeter_km' => (float) $perimeter,
            ]);
        }

        return $normalized;
    }
}
