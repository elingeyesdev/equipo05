<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoluntarioIncendio extends Model
{
    protected $table = 'voluntario_incendio';

    protected $fillable = [
        'voluntario_id',
        'incendio_id',
        'rol',
        'estado',
    ];

    public function voluntario(): BelongsTo
    {
        return $this->belongsTo(Voluntario::class);
    }

    public function incendio(): BelongsTo
    {
        return $this->belongsTo(Incendio::class);
    }
}
