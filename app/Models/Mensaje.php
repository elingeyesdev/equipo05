<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';
    protected $primaryKey = 'mensajeid';
    public $timestamps = false; // si tu tabla no tiene created_at/updated_at

    protected $fillable = [
        'conversacionid',
        'usuarioid',
        'asunto',
        'contenido',
        'fechaenvio',
        'leido',
    ];

    // ✅ el "remitente" ahora es usuarioid
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuarioid', 'usuarioid');
    }

    public function conversacion()
    {
        return $this->belongsTo(Conversacion::class, 'conversacionid', 'conversacionid');
    }
}
