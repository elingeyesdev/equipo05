<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FocoTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'foco_incendio_id',
        'recorded_at',
        'coordinates',
        'intensidad',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'coordinates' => 'array',
    ];

    public function foco()
    {
        return $this->belongsTo(FocoIncendio::class, 'foco_incendio_id');
    }
}
