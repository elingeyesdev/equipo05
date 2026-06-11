<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProductoHistorial extends Model
{
    protected $connection = 'inventario';

    protected $table = 'categorias_productos_historial';

    protected $primaryKey = 'id_historial';

    public $timestamps = false;

    protected $fillable = [
        'id_categoria',
        'accion',
        'usuario_ci',
        'datos_anteriores',
        'datos_nuevos',
        'created_at',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'created_at' => 'datetime',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriasProducto::class, 'id_categoria', 'id_categoria');
    }
}
