<?php

namespace App\Models\Ext;

use App\Models\TransparenciaModel;

class ExtProducto extends TransparenciaModel
{
    protected $table = 'ext_productos';
    protected $primaryKey = 'productoid';
    public $timestamps = true;

    protected $fillable = [
        'idexterno',
        'categoriaid',
        'nombre',
        'descripcion',
        'unidad_medida',
    ];

    public function categoria()
    {
        return $this->belongsTo(ExtCategoriaProducto::class, 'categoriaid', 'categoriaid');
    }
}
