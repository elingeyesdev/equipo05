<?php
namespace App\Models\Ext;

use App\Models\RecursoExterno;
use Illuminate\Database\Eloquent\Builder;

class ExtEspecie extends RecursoExterno
{
    protected static function booted()
    {
        static::addGlobalScope('tipo', function (Builder $builder) {
            $builder->where('tipo', 'especie');
        });
    }
    protected $attributes = ['tipo' => 'especie'];
}