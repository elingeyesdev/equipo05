<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'incendio_id',
        'mensaje',
        'tipo',
        'leido',
    ];

    protected function casts(): array
    {
        return [
            'leido' => 'boolean',
        ];
    }

    public function incendio(): BelongsTo
    {
        return $this->belongsTo(Incendio::class);
    }
}
