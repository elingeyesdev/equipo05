<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DonacionesDinero
 *
 * @property $id_donacion_dinero
 * @property $id_donacion
 * @property $monto
 * @property $moneda
 * @property $metodo_pago
 * @property $referencia_pago
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class DonacionesDinero extends Model
{
    protected $connection = 'inventario';
    protected $table = 'donaciones_dinero';
    protected $primaryKey = 'id_donacion_dinero';
    public $timestamps = false;
    protected $perPage = 20;

    protected $fillable = [
        'id_donacion',
        'monto',
        'moneda',
        'metodo_pago',
        'referencia_pago'
    ];

    public function donacion()
    {
        return $this->belongsTo(Donacione::class, 'id_donacion', 'id_donacion');
    }
}



