<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialIncendio extends Model
{
    protected $table = 'historial_incendios';

    protected $fillable = [
        'incendio_id',
        'estado_anterior',
        'estado_nuevo',
        'descripcion',
        'fecha_cambio',
    ];

    protected function casts(): array
    {
        return [
            'fecha_cambio' => 'datetime',
        ];
    }

    public function incendio(): BelongsTo
    {
        return $this->belongsTo(Incendio::class);
    }
}
