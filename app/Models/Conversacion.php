<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversacion extends Model
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
