<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 *
 * @property $id_rol
 * @property $nombre_rol
 * @property $descripcion
 * @property $estado
 * @property $fecha_creacion
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Role extends Model
{
    protected $connection = 'inventario';
    protected $table = 'roles';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nombre_rol',
        'descripcion_rol',
    ];

    /**
     * Relación con usuarios
     */
    public function usuarios()
    {
        return $this->hasMany(\Modules\Inventario\Models\Usuario::class, 'id_rol', 'id_rol');
    }
}






