<?php

namespace App\Models\Ext;

use App\Models\TransparenciaModel;

class ExtEstante extends TransparenciaModel
{
    protected $table = 'ext_estantes';
    protected $primaryKey = 'estanteid';
    public $timestamps = true;

    protected $fillable = [
        'idexterno',
        'almacenid',
        'codigo_estante',
        'descripcion',
    ];

    public function almacen()
    {
        return $this->belongsTo(ExtAlmacen::class, 'almacenid', 'almacenid');
    }

    public function espacios()
    {
        return $this->hasMany(ExtEspacio::class, 'estanteid', 'estanteid');
    }
}
