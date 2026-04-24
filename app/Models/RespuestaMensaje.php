<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaMensaje extends Model
{
    protected $table = 'respuestasmensajes';
    protected $primaryKey = 'respuestaid';
    public $timestamps = false;

    protected $fillable = [
        'mensajeid',
        'usuarioid',
        'contenido',
        'fecharespuesta',
    ];

    public function mensaje()
    {
        return $this->belongsTo(Mensaje::class, 'mensajeid', 'mensajeid');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuarioid', 'usuarioid');
    }

    // alias opcional por si en algún lado usas ->autor
    public function autor()
    {
        return $this->belongsTo(Usuario::class, 'usuarioid', 'usuarioid');
    }
}
