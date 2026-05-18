<?php

namespace App\Models\Ext;

use App\Models\TransparenciaModel;

class ExtAlmacen extends TransparenciaModel
{
    protected $table = 'ext_almacenes';
    protected $primaryKey = 'almacenid';
    public $timestamps = true;

    protected $fillable = [
        'idexterno',
        'nombre',
        'direccion',
        'latitud',
        'longitud',
    ];

    public function estantes()
    {
        return $this->hasMany(ExtEstante::class, 'almacenid', 'almacenid');
    }
}
