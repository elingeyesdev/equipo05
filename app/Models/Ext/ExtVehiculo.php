<?php
namespace App\Models\Ext;

use App\Models\RecursoExterno;
use Illuminate\Database\Eloquent\Builder;

class ExtVehiculo extends RecursoExterno
{
    protected static function booted()
    {
        static::addGlobalScope('tipo', function (Builder $builder) {
            $builder->where('tipo', 'vehiculo');
        });
    }
    protected $attributes = ['tipo' => 'vehiculo'];
}