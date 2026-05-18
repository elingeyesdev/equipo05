<?php
namespace App\Models;

use App\Models\TransparenciaModel;

class Conversacion extends TransparenciaModel
{
    protected $table = 'conversaciones';
    protected $primaryKey = 'conversacionid';

    protected $fillable = ['tipo'];

    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class,
            'conversacion_usuarios',
            'conversacionid',
            'usuarioid'
        )->withPivot('ultimo_leido');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'conversacionid', 'conversacionid');
    }
}
