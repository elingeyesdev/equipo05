<?php

namespace App\Models\Ext;

use App\Models\TransparenciaModel;

class ExtCategoriaProducto extends TransparenciaModel
{
    protected $table = 'ext_categorias_productos';
    protected $primaryKey = 'categoriaid';
    public $timestamps = true;

    protected $fillable = [
        'idexterno',
        'nombre',
    ];

    public function productos()
    {
        return $this->hasMany(ExtProducto::class, 'categoriaid', 'categoriaid');
    }
}
