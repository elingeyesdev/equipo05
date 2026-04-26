<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoBiomasa extends Model
{
    use HasFactory;

    protected $table = 'tipo_biomasa';
    
    // Disable automatic plural table name
    public $timestamps = true;

    protected $fillable = [
        'tipo_biomasa',
        'color',
        'modificador_intensidad',
    ];

    protected $casts = [
        'modificador_intensidad' => 'decimal:2',
    ];

    /**
     * Biomasas de este tipo
     */
    public function biomasas()
    {
        return $this->hasMany(\App\Models\Biomasa::class, 'tipo_biomasa_id');
    }
}
