<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voluntario extends Model
{
    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
        'estado',
    ];

    public function voluntarioIncendios(): HasMany
    {
        return $this->hasMany(VoluntarioIncendio::class);
    }
}
