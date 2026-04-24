<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Producto
 *
 * @property $id_producto
 * @property $id_categoria
 * @property $nombre
 * @property $descripcion
 * @property $unidad_medida
 *
 * @property CategoriasProducto $categoriasProducto
 * @property DonacionDetalle[] $donacionDetalles
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Producto extends Model
{
    protected $connection = 'inventario';
    
    protected $perPage = 20;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_producto';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_categoria', 'nombre', 'descripcion', 'unidad_medida'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoriaProducto()
    {
        return $this->belongsTo(\App\Models\CategoriasProducto::class, 'id_categoria', 'id_categoria');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoriasProducto()
    {
        return $this->belongsTo(\App\Models\CategoriasProducto::class, 'id_categoria', 'id_categoria');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function donacionDetalles()
    {
        return $this->hasMany(\App\Models\DonacionDetalle::class, 'id_producto', 'id_producto');
    }
    
}



