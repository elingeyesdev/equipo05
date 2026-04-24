<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RegistrosSalida
 *
 * @property $id_salida
 * @property $id_paquete
 * @property $fecha_salida
 * @property $destino
 * @property $observaciones
 *
 *
 * @property Paquete $paquete
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class RegistrosSalida extends Model
{
    protected $connection = 'inventario';
    protected $table = 'registros_salida';
    protected $primaryKey = 'id_salida';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_paquete', 'fecha_salida', 'destino', 'encargado', 'observaciones'];


    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paquete()
    {
        return $this->belongsTo(\App\Models\Paquete::class, 'id_paquete', 'id_paquete');
    }
    
}



