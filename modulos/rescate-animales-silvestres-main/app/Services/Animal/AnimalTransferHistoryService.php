<?php

namespace Modules\Rescate\Services\Animal;

use Modules\Rescate\Models\AnimalFile;
use Modules\Rescate\Models\AnimalHistory;
use Modules\Rescate\Models\Center;
use Modules\Rescate\Models\Transfer;

class AnimalTransferHistoryService
{
    /**
     * Registra en historial el primer traslado originado por un hallazgo (sin hoja aún).
     */
    public function logFirstTransfer(Transfer $transfer, ?int $reporteId = null): void
    {
        AnimalHistory::create([
            'animal_file_id' => null,
            'old_values' => null,
            'new_values' => json_encode([
                'transfer' => [
                    'id' => $transfer->id,
                    'persona_id' => $transfer->persona_id,
                    'reporte_id' => $reporteId,
                    'centro_id' => $transfer->centro_id,
                    'observaciones' => $transfer->observaciones,
                    'primer_traslado' => true,
                    'latitud' => $transfer->latitud ?? null,
                    'longitud' => $transfer->longitud ?? null,
                    'created_at' => $transfer->created_at ? $transfer->created_at->toDateTimeString() : null,
                ],
            ], JSON_UNESCAPED_UNICODE),
            'estado_anterior' => 'Hallazgo aprobado',
            'estado_nuevo' => 'En traslado',
            'observaciones' => 'Primer traslado desde reporte de hallazgo',
            'changed_at' => $transfer->created_at,
        ]);
    }

    /**
     * Registra en historial un traslado entre centros (con hoja de vida).
     * Incluye el centro anterior y el nuevo.
     */
    public function logInternalTransfer(Transfer $transfer): void
    {
        if (empty($transfer->animal_id)) {
            return;
        }

        $animalFile = AnimalFile::where('animal_id', $transfer->animal_id)
            ->orderByDesc('id')
            ->first();
        if (!$animalFile) {
            return;
        }

        // Centro anterior: último traslado anterior a este para el mismo animal
        $prev = Transfer::where('animal_id', $transfer->animal_id)
            ->where('id', '<', $transfer->id)
            ->orderByDesc('id')
            ->first();
        $oldCenter = $prev ? $prev->center : null;
        $newCenter = $transfer->center ?: Center::find($transfer->centro_id);

        $oldValues = null;
        if ($oldCenter) {
            $oldValues = [
                'centro' => [
                    'id' => $oldCenter->id,
                    'nombre' => $oldCenter->nombre,
                ],
            ];
        }

        $newValues = [
            'transfer' => [
                'id' => $transfer->id,
                'persona_id' => $transfer->persona_id,
                'centro_id' => $transfer->centro_id,
                'observaciones' => $transfer->observaciones,
                'primer_traslado' => false,
                'latitud' => $transfer->latitud ?? null,
                'longitud' => $transfer->longitud ?? null,
                'created_at' => $transfer->created_at ? $transfer->created_at->toDateTimeString() : null, // Guardar fecha original del traslado
            ],
            'centro' => $newCenter ? [
                'id' => $newCenter->id,
                'nombre' => $newCenter->nombre,
            ] : null,
        ];

        AnimalHistory::create([
            'animal_file_id' => $animalFile->id,
            'old_values' => $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
            'new_values' => json_encode($newValues, JSON_UNESCAPED_UNICODE),
            'estado_anterior' => $oldCenter?->nombre ?? 'Centro anterior',
            'estado_nuevo' => $newCenter?->nombre ?? 'Centro destino',
            'observaciones' => 'Registro de traslado entre centros',
            'changed_at' => $transfer->created_at,
        ]);
    }
}


