<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simulacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'nombre',
        'duracion', // in minutes or as a string (choose convention)
        'focos_activos',
        'num_voluntarios_enviados',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'duracion' => 'integer',
        'focos_activos' => 'integer',
        'num_voluntarios_enviados' => 'integer',
    ];

    // relationships can be added later (e.g., focos, usuarios)
    /**
     * Simulation creator (user)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Volunteers assigned to the simulation
     */
    public function volunteers()
    {
        return $this->belongsToMany(User::class, 'simulacion_user')->withPivot(['role','assigned_at'])->withTimestamps();
    }

    /**
     * Focos (fire spots) that belong to this simulation
     */
    public function focos()
    {
        return $this->hasMany(FocoIncendio::class, 'simulacion_id');
    }

    /**
     * Predictions generated for this simulation
     */
    public function predictions()
    {
        return $this->hasMany(\App\Models\Prediction::class);
    }
}
