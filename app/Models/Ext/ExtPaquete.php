<?php

namespace App\Models\Ext;

use Illuminate\Database\Eloquent\Model;

class ExtPaquete extends Model
{
    protected $table = 'ext_paquetes';
    
    protected $fillable = [
        'codigo_paquete', 
        'estado', 
        'fecha_creacion', 
        'datos_gateway', 
        'ultimo_sync'
    ];

    protected $casts = [
        'datos_gateway' => 'array', // Convierte JSON a Array automáticamente
        'fecha_creacion' => 'datetime',
        'ultimo_sync' => 'datetime',
    ];
}