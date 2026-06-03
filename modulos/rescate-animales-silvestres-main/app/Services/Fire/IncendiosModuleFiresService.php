<?php

namespace Modules\Rescate\Services\Fire;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Modules\Incendios\Models\FocosIncendio;
use Modules\Rescate\Models\Report;

class IncendiosModuleFiresService
{
    public function isAvailable(): bool
    {
        try {
            return Schema::connection('incendios')->hasTable('focos_incendios');
        } catch (\Throwable) {
            return false;
        }
    }

    public function getFocos(): Collection
    {
        if (! $this->isAvailable()) {
            return collect();
        }

        return FocosIncendio::query()
            ->whereNotNull('coordenadas')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function formatForMap(?Collection $focos = null): array
    {
        $focos = $focos ?? $this->getFocos();

        $localByIncendio = Report::query()
            ->where('aprobado', 1)
            ->whereNotNull('incendio_id')
            ->get()
            ->groupBy(fn (Report $report) => (string) $report->incendio_id);

        return $focos->map(function (FocosIncendio $foco) use ($localByIncendio) {
            $coords = $this->parseCoordinates($foco->coordenadas);
            if ($coords === null) {
                return null;
            }

            [$lat, $lng] = $coords;
            $severity = $this->intensityToSeverity((float) ($foco->intensidad ?? 0));
            $local = $localByIncendio->get((string) $foco->id, collect());

            return [
                'id' => $foco->id,
                'lat' => $lat,
                'lng' => $lng,
                'nombre_reportante' => 'Módulo Incendios',
                'telefono_contacto' => null,
                'fecha_hora' => $foco->fecha?->toIso8601String(),
                'nombre_lugar' => $foco->ubicacion ?: ('Foco #'.$foco->id),
                'comentario_adicional' => 'Intensidad registrada: '.($foco->intensidad ?? 'N/D'),
                'nivel_gravedad' => $severity,
                'creado' => $foco->created_at?->toIso8601String(),
                'color' => $this->severityColor($severity),
                'has_local_reports' => $local->isNotEmpty(),
                'local_reports_count' => $local->count(),
                'simulado' => false,
                'source' => 'incendios',
                'incendios_url' => route('incendios.focos-incendios.show', $foco->id),
            ];
        })->filter()->values()->all();
    }

    /**
     * @return array{0: float, 1: float}|null
     */
    public function parseCoordinates(mixed $raw): ?array
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $raw = $decoded;
            } else {
                $parts = array_map('trim', explode(',', $raw));
                if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    return [(float) $parts[0], (float) $parts[1]];
                }

                return null;
            }
        }

        if (is_array($raw)) {
            if (isset($raw['lat'], $raw['lng'])) {
                return [(float) $raw['lat'], (float) $raw['lng']];
            }

            if (isset($raw[0], $raw[1]) && is_numeric($raw[0]) && is_numeric($raw[1])) {
                return [(float) $raw[0], (float) $raw[1]];
            }
        }

        return null;
    }

    public function intensityToSeverity(float $intensity): string
    {
        return match (true) {
            $intensity >= 8 => 'Fuera de control',
            $intensity >= 6 => 'Activo',
            $intensity >= 4 => 'Contenido',
            default => 'Controlado',
        };
    }

    public function severityColor(string $severity): string
    {
        $severity = strtolower(trim($severity));

        return match ($severity) {
            'fuera de control' => '#dc3545',
            'activo' => '#ff8800',
            'contenido' => '#ffc107',
            'controlado' => '#28a745',
            default => '#6c757d',
        };
    }
}
