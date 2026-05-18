<?php
namespace App\Models;
use App\Models\TransparenciaModel;

class DetallesAsignacion extends TransparenciaModel
{
    protected $table = 'detallesasignacion';
    protected $primaryKey = 'detalleid';
    public $timestamps = false;
    protected $fillable = ['asignacionid','concepto','cantidad','preciounitario','imagenurl'];

    public function asignacion() {
        return $this->belongsTo(Asignacion::class, 'asignacionid', 'asignacionid');
    }
}
