<?php

namespace Modules\Rescate\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalCondition extends Model
{
    protected $connection = 'rescate';
    protected $table = 'animal_conditions';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'severidad', // 1..5
        'activo',
    ];
}





