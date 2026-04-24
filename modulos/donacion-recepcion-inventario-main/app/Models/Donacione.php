<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Donacione
 *
 * @property $id_donacion
 * @property $id_donante
 * @property $tipo
 * @property $id_campana
 * @property $id_punto_recoleccion
 * @property $observaciones
 * @property $fecha
 * @property $deleted_at
 * @property $deleted_by
 * @property $deleted_reason
 *
 * @property DonacionDetalle[] $donacionDetalles
 * @property DonacionesDinero $donacionesDinero
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Donacione extends Model
{
    protected $connection = 'inventario';
    use SoftDeletes;

    protected $table = 'donaciones';
    protected $primaryKey = 'id_donacion';
    public $timestamps = false;
    protected $perPage = 20;

    protected $fillable = [
        'id_donante',
        'tipo',
        'id_campana',
        'id_punto_recoleccion',
        'observaciones',
        'fecha',
        'deleted_reason',
        'deleted_by',
        'ci_usuario_registro'
    ];

    public function detalles()
    {
        return $this->hasMany(DonacionDetalle::class, 'id_donacion', 'id_donacion');
    }

    public function dinero()
    {
        return $this->hasOne(DonacionesDinero::class, 'id_donacion', 'id_donacion');
    }

    public function donante()
    {
        return $this->belongsTo(Donante::class, 'id_donante', 'id_donante');
    }

    public function campana()
    {
        return $this->belongsTo(Campana::class, 'id_campana', 'id_campana');
    }
}



