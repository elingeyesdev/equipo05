<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Paquete
 *
 * @property $id_paquete
 * @property $codigo_paquete
 * @property $fecha_creacion
 * @property $id_usuario
 * @property $id_solicitud
 * @property $estado
 * @property $deleted_at
 * @property $deleted_by
 * @property $codigo_solicitud_externa
 *
 * @property SolicitudesAyuda $solicitudesAyuda
 * @property Usuario $usuario
 * @property PaqueteDetalle[] $paqueteDetalles
 * @property RegistrosSalida[] $registrosSalidas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Paquete extends Model
{
    protected $connection = 'inventario';
    use SoftDeletes;

    protected $table = 'paquetes';
    protected $primaryKey = 'id_paquete';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['codigo_paquete', 'fecha_creacion', 'id_solicitud', 'estado', 'deleted_by', 'deleted_reason', 'codigo_solicitud_externa', 'ci_usuario_registro'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * Relación comentada porque SolicitudesAyuda no existe
     */
    // public function solicitud()
    // {
    //     return $this->belongsTo(\App\Models\SolicitudesAyuda::class, 'id_solicitud', 'id_solicitud');
    // }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * Relación comentada porque SolicitudesAyuda no existe
     */
    // public function solicitudesAyuda()
    // {
    //     return $this->belongsTo(\App\Models\SolicitudesAyuda::class, 'id_solicitud', 'id_solicitud');
    // }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detalles()
    {
        return $this->hasMany(\App\Models\PaqueteDetalle::class, 'id_paquete', 'id_paquete');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function paqueteDetalles()
    {
        return $this->hasMany(\App\Models\PaqueteDetalle::class, 'id_paquete', 'id_paquete');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function registrosSalidas()
    {
        return $this->hasMany(\App\Models\RegistrosSalida::class, 'id_paquete', 'id_paquete');
    }

}



