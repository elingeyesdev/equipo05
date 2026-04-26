<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FocosIncendio
 *
 * @property $id
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class FocosIncendio extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fecha',
        'ubicacion',
        'coordenadas',
        'intensidad',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'coordenadas' => 'array',
        'intensidad' => 'float',
    ];

    /**
     * Simulaciones que incluyen este foco (many-to-many)
     */
    public function simulaciones()
    {
        return $this->belongsToMany(\App\Models\Simulacione::class, 'foco_simulacion', 'foco_incendio_id', 'simulacion_id')
                    ->withPivot(['agregado_at', 'activo'])
                    ->withTimestamps();
    }

    /**
     * Predicciones generadas para este foco
     */
    public function predictions()
    {
        return $this->hasMany(\App\Models\Prediction::class, 'foco_incendio_id');
    }

    /**
     * Movement / intensity tracks for this foco
     */
    public function tracks()
    {
        return $this->hasMany(\App\Models\FocoTrack::class, 'foco_incendio_id');
    }
}
