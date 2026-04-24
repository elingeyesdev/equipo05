<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Campania extends Model
{
    protected $table = 'campanias';
    protected $primaryKey = 'campaniaid';
    public $timestamps = false;
    protected $fillable = [
        'idexterno',
        'titulo',
        'descripcion',
        'fechainicio',
        'fechafin',
        'metarecaudacion',
        'montorecaudado',
        'usuarioidcreador',
        'activa',
        'imagenurl',
        'fechacreacion',
    ];

    public function creador() {
        return $this->belongsTo(Usuario::class, 'usuarioidcreador', 'usuarioid');
    }

    public function donaciones() {
        return $this->hasMany(Donacion::class, 'campaniaid', 'campaniaid');
    }

    public function asignaciones() {
        return $this->hasMany(Asignacion::class, 'campaniaid', 'campaniaid');
    }
}
