<?php

namespace App\Models\Ext;

use App\Models\TransparenciaModel;

class ExtPaquete extends TransparenciaModel
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