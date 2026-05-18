<?php

namespace App\Models;

use App\Models\TransparenciaModel;

class RespuestaMensaje extends TransparenciaModel
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
