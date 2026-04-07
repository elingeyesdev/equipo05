<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function incendios(): BelongsToMany
    {
        return $this->belongsToMany(Incendio::class, 'voluntario_incendio')
            ->withPivot(['rol', 'estado'])
            ->withTimestamps();
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido}");
    }
}
