<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetallesAsignacion extends Model
{
    protected $table = 'detallesasignacion';
    protected $primaryKey = 'detalleid';
    public $timestamps = false;
    protected $fillable = ['asignacionid','concepto','cantidad','preciounitario','imagenurl'];

    public function asignacion() {
        return $this->belongsTo(Asignacion::class, 'asignacionid', 'asignacionid');
    }
}
