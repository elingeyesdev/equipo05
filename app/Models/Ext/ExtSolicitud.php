<?php
namespace App\Models\Ext;

use App\Models\RecursoExterno;
use Illuminate\Database\Eloquent\Builder;

class ExtSolicitud extends RecursoExterno
{
    // Scope Global: Siempre filtra por tipo 'solicitud'
    protected static function booted()
    {
        static::addGlobalScope('tipo', function (Builder $builder) {
            $builder->where('tipo', 'solicitud');
        });
    }
    
    // Al crear uno nuevo, le pone el tipo automáticamente
    protected $attributes = ['tipo' => 'solicitud'];
}