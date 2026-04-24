<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;

class PaqueteDetalle extends Model
{
    protected $connection = 'inventario';
    protected $table = 'paquete_detalles';
    protected $primaryKey = 'id_detalle';
    public $timestamps = false;
    protected $perPage = 20;

    protected $fillable = [
        'id_paquete',
        'id_detalle_donacion',
        'cantidad_usada'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'id_paquete', 'id_paquete');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function donacionDetalle()
    {
        return $this->belongsTo(DonacionDetalle::class, 'id_detalle_donacion', 'id_detalle');
    }
}



