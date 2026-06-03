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
    ];

    protected $casts = [
        'changed_at' => 'datetime',
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

    public function animalFile()
    {
        return $this->belongsTo(\Modules\Rescate\Models\AnimalFile::class, 'animal_file_id', 'id');
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
