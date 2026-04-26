<?php

namespace Modules\Incendios\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prediction extends Model
{
    protected $connection = 'incendios';
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'foco_incendio_id',
        'predicted_at',
        'path',
        'meta',
        'user_id',
        'ci_usuario',
    ];

    protected $casts = [
        'predicted_at' => 'datetime',
        'path' => 'array',
        'meta' => 'array',
    ];

    /**
     * Foco de incendio al que pertenece esta predicción
     */
    public function focoIncendio()
    {
        return $this->belongsTo(\Modules\Incendios\Models\FocosIncendio::class, 'foco_incendio_id');
    }
}
