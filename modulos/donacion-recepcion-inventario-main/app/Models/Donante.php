<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class Donante
 *
 * @property $id_donante
 * @property $nombre
 * @property $tipo
 * @property $email
 * @property $telefono
 * @property $direccion
 * @property $fecha_registro
 * @property $deleted_at
 * @property $deleted_by
 *
 * @property Donacione[] $donaciones
 * @property SolicitudesRecoleccion[] $solicitudesRecoleccions
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Donante extends Model
{
    protected $connection = 'inventario';
    use SoftDeletes, HasApiTokens;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'donantes';
    protected $primaryKey = 'id_donante';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    
    protected $fillable = ['nombre', 'tipo', 'email', 'telefono', 'direccion', 'fecha_registro', 'deleted_by', 'password', 'cambiar_password'];

    /**
     * Campos ocultos en serialización JSON
     */
    protected $hidden = ['password'];
    
    /**
     * Los atributos que deben ser casteados.
     */
    protected $casts = [
        'cambiar_password' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_donante';
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function donaciones()
    {
        return $this->hasMany(\Modules\Inventario\Models\Donacione::class, 'id_donante', 'id_donante');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function solicitudesRecoleccions()
    {
        return $this->hasMany(\Modules\Inventario\Models\SolicitudesRecoleccion::class, 'id_donante', 'id_donante');
    }
    
}






