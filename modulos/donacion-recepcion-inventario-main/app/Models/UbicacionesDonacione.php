<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UbicacionesDonacione
 *
 * @property $id_ubicacion
 * @property $id_detalle
 * @property $id_espacio
 * @property $fecha_ingreso
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class UbicacionesDonacione extends Model
{
    protected $connection = 'inventario';
    protected $table = 'ubicaciones_donaciones';
    protected $primaryKey = 'id_ubicacion';
    public $timestamps = false;
    protected $perPage = 20;

    protected $fillable = [
        'id_detalle',
        'id_espacio',
        'fecha_ingreso',
        'cantidad_ubicada'
    ];

    public function donacionDetalle()
    {
        return $this->belongsTo(DonacionDetalle::class, 'id_detalle', 'id_detalle');
    }

    public function detalle()
    {
        return $this->belongsTo(DonacionDetalle::class, 'id_detalle', 'id_detalle');
    }

    public function espacio()
    {
        return $this->belongsTo(Espacio::class, 'id_espacio', 'id_espacio');
    }
}



