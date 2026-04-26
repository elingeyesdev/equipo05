<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Simulacione
 *
 * @property $id
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Simulacione extends Model
{
    use SoftDeletes;
    
    protected $perPage = 20;

    protected $fillable = [
        'nombre',
        'fecha',
        'duracion',
        'focos_activos',
        'num_voluntarios_enviados',
        'estado',
        'admin_id',
        'ci_usuario',
        'temperature',
        'humidity',
        'wind_speed',
        'wind_direction',
        'simulation_speed',
        'fire_risk',
        'map_center_lat',
        'map_center_lng',
        'public',
        'initial_fires',
        'mitigation_strategies',
        'auto_stopped',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'duracion' => 'integer',
        'focos_activos' => 'integer',
        'num_voluntarios_enviados' => 'integer',
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'wind_speed' => 'decimal:2',
        'wind_direction' => 'integer',
        'simulation_speed' => 'decimal:1',
        'fire_risk' => 'integer',
        'map_center_lat' => 'decimal:7',
        'map_center_lng' => 'decimal:7',
        'initial_fires' => 'array',
        'mitigation_strategies' => 'array',
        'public' => 'boolean',
        'auto_stopped' => 'boolean',
    ];

    /**
     * Administrador que creó esta simulación
     */
    public function admin()
    {
        return $this->belongsTo(\App\Models\Administrador::class, 'admin_id');
    }

    /**
     * Focos de incendio incluidos en esta simulación (many-to-many)
     */
    public function focos()
    {
        return $this->belongsToMany(\App\Models\FocosIncendio::class, 'foco_simulacion', 'simulacion_id', 'foco_incendio_id')
                    ->withPivot(['agregado_at', 'activo'])
                    ->withTimestamps();
    }

    /**
     * Historial de propagación de focos durante la simulación
     */
    public function fireHistory()
    {
        return $this->hasMany(\App\Models\SimulationFireHistory::class, 'simulacion_id');
    }
}
