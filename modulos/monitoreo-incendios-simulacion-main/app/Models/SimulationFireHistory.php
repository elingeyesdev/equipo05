<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationFireHistory extends Model
{
    protected $table = 'simulation_fire_history';

    protected $fillable = [
        'simulacion_id',
        'fire_id',
        'time_step',
        'lat',
        'lng',
        'intensity',
        'spread',
        'active',
    ];

    protected $casts = [
        'fire_id' => 'string',
        'time_step' => 'integer',
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'intensity' => 'decimal:2',
        'spread' => 'decimal:3',
        'active' => 'boolean',
    ];

    public function simulacion()
    {
        return $this->belongsTo(Simulacione::class, 'simulacion_id');
    }
}
