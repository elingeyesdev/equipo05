<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incendio extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'latitud',
        'longitud',
        'estado',
        'nivel_riesgo',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'datetime',
            'fecha_fin' => 'datetime',
            'latitud' => 'decimal:7',
            'longitud' => 'decimal:7',
        ];
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }

    public function voluntarioIncendios(): HasMany
    {
        return $this->hasMany(VoluntarioIncendio::class);
    }

    public function voluntarios(): BelongsToMany
    {
        return $this->belongsToMany(Voluntario::class, 'voluntario_incendio')
            ->withPivot(['rol', 'estado'])
            ->withTimestamps();
    }

    public function historial(): HasMany
    {
        return $this->hasMany(HistorialIncendio::class);
    }

    /** Incendios visibles en el panel de monitoreo (activo o controlado). */
    public function scopeEnMonitoreo(Builder $query): Builder
    {
        return $query->whereIn('estado', ['activo', 'controlado']);
    }
}

