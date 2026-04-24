<?php

namespace App\Models\Ext;

use Illuminate\Database\Eloquent\Model;

class ExtCategoriaProducto extends Model
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
