<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecursoExterno extends Model
{
    protected $table = 'recursos_externos';

    protected $fillable = [
        'tipo',            // 'solicitud', 'vehiculo', 'especie'
        'gateway_id',      // 4, 1, 2...
        'identificador',   // GRT-8116, 4696SSN, Ave
        'datos_extra',     // Info ligera del listado
        'response_detalle',// <--- AQUÍ VA EL JSON GIGANTE
        'detalle_cached_at'
    ];

    protected $casts = [
        'datos_extra' => 'array',
        'response_detalle' => 'array', // ¡Clave! Convierte JSON a Array automáticamente
        'detalle_cached_at' => 'datetime',
    ];
    public function getIdentificadorAttribute($value)
    {
        return $value
            ?? data_get($this->response_detalle, 'codigo')
            ?? data_get($this->response_detalle, 'codigo_paquete')
            ?? data_get($this->response_detalle, 'id')
            ?? null;
    }

}