<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Inventario\Models\Donacione;
use Modules\Inventario\Models\PaqueteDetalle;

/**
 * Class DonacionDetalle
 *
 * @property $id_detalle
 * @property $id_donacion
 * @property $id_producto
 * @property $cantidad
 * @property $unidad_medida
 * @property $descripcion
 * @property $id_talla
 * @property $id_genero
 * @property $fecha_caducidad
 *
 * @property Producto $producto
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class DonacionDetalle extends Model
{
    protected $connection = 'inventario';
    protected $table = 'donacion_detalles';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;
    protected $perPage = 20;

    protected $fillable = [
        'id_donacion',
        'id_producto',
        'cantidad',
        'unidad_medida',
        'descripcion',
        'id_talla',
        'id_genero',
        'fecha_caducidad'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function donacion()
    {
        return $this->belongsTo(Donacione::class, 'id_donacion', 'id_donacion');
    }

    public function ubicaciones()
    {
        return $this->hasMany(UbicacionesDonacione::class, 'id_detalle', 'id_detalle');
    }

    public function paqueteDetalles()
    {
        return $this->hasMany(PaqueteDetalle::class, 'id_detalle_donacion', 'id_detalle');
    }
}






