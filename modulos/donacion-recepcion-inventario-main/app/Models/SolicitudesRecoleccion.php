<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SolicitudesRecoleccion
 *
 * @property $id_solicitud
 * @property $id_donante
 * @property $id_recolector
 * @property $direccion_recoleccion
 * @property $fecha_programada
 * @property $observaciones
 * @property $estado
 * @property $fecha_creacion
 *
 * @property Donante $donante
 * @property Usuario $usuario
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class SolicitudesRecoleccion extends Model
{
    protected $connection = 'inventario';
    protected $table = 'solicitudes_recoleccion';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['id_donante', 'id_recolector', 'id_campana', 'direccion_recoleccion', 'fecha_programada', 'observaciones', 'estado', 'fecha_creacion'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function donante()
    {
        return $this->belongsTo(\Modules\Inventario\Models\Donante::class, 'id_donante', 'id_donante');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(\Modules\Inventario\Models\Usuario::class, 'id_recolector', 'id_usuario');
    }

    public function campana()
    {
        return $this->belongsTo(\Modules\Inventario\Models\Campana::class, 'id_campana', 'id_campana');
    }
}






