<?php

namespace Modules\Rescate\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalHistory extends Model
{
    protected $connection = 'rescate';

    protected $table = 'animal_histories';

    public $timestamps = false;

    protected $fillable = [
        'animal_file_id',
        'changed_at',
        'estado_anterior',
        'estado_nuevo',
        'observaciones',
        'old_values',
        'new_values',
        'valores_antiguos',
        'valores_nuevos',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
        'observaciones' => 'json',
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    public function getValoresAntiguosAttribute(): ?array
    {
        return $this->decodeHistoryPayload(
            $this->attributes['valores_antiguos'] ?? $this->attributes['old_values'] ?? null
        );
    }

    public function getValoresNuevosAttribute(): ?array
    {
        return $this->decodeHistoryPayload(
            $this->attributes['valores_nuevos'] ?? $this->attributes['new_values'] ?? null
        );
    }

    public function setValoresAntiguosAttribute(mixed $value): void
    {
        $this->attributes['old_values'] = (is_array($value) || is_object($value)) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }

    public function setValoresNuevosAttribute(mixed $value): void
    {
        $this->attributes['new_values'] = (is_array($value) || is_object($value)) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    }

    /**
     * Registro compatible con el esquema unificado (estado_anterior/estado_nuevo NOT NULL).
     */
    public static function recordEvent(
        ?int $animalFileId,
        string $estadoAnterior,
        string $estadoNuevo,
        ?string $observaciones = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?\DateTimeInterface $changedAt = null,
    ): self {
        return self::create([
            'animal_file_id' => $animalFileId,
            'changed_at' => $changedAt ?? now(),
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'observaciones' => $observaciones ?? '',
            'old_values' => $oldValues !== null
                ? json_encode($oldValues, JSON_UNESCAPED_UNICODE)
                : null,
            'new_values' => $newValues !== null
                ? json_encode($newValues, JSON_UNESCAPED_UNICODE)
                : null,
        ]);
    }

    public function animalFile()
    {
        return $this->belongsTo(\Modules\Rescate\Models\AnimalFile::class, 'animal_file_id', 'id');
    }

    public static function newValuesColumn(): string
    {
        static $column = null;
        if ($column === null) {
            $column = \Illuminate\Support\Facades\Schema::connection('rescate')->hasColumn(
                (new self)->getTable(),
                'new_values'
            ) ? 'new_values' : 'valores_nuevos';
        }

        return $column;
    }

    public static function jsonPath(string $segments): string
    {
        return '('.self::newValuesColumn().'::json'.$segments.')';
    }

    private function decodeHistoryPayload(mixed $raw): ?array
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_array($raw)) {
            return $raw;
        }

        $decoded = json_decode((string) $raw, true);

        return is_array($decoded) ? $decoded : null;
    }
}
