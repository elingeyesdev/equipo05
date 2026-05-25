<?php
namespace App\Models;

use App\Support\UnifiedPostgres;
use App\Models\TransparenciaModel;

class Conversacion extends TransparenciaModel
{
    protected $table = 'conversaciones';
    protected $primaryKey = 'conversacionid';

    protected $fillable = ['tipo'];

    public function usuarios()
    {
        $pivot = UnifiedPostgres::enabled()
            ? 'transparencia.conversacion_usuarios'
            : 'conversacion_usuarios';

        return $this->belongsToMany(
            Usuario::class,
            $pivot,
            'conversacionid',
            'usuarioid'
        )
            ->using(ConversacionUsuario::class)
            ->withPivot('ultimo_leido');
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class, 'conversacionid', 'conversacionid');
    }
}
