<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    protected $table = 'asignaciones';
    protected $primaryKey = 'asignacionid';
    public $timestamps = false;
    protected $fillable = [
        'campaniaid','descripcion','monto','fechaasignacion',
        'imagenurl','usuarioid','comprobante'
    ];

    public function campania() {
        return $this->belongsTo(Campania::class, 'campaniaid', 'campaniaid');
    }

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'usuarioid', 'usuarioid');
    }

    public function detalles() {
        return $this->hasMany(DetallesAsignacion::class, 'asignacionid', 'asignacionid');
    }

    public function donacionesPivot() {
        return $this->hasMany(DonacionesAsignacion::class, 'asignacionid', 'asignacionid');
    }
}
