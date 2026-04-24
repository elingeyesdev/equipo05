<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DonacionesAsignacion extends Model
{
    protected $table = 'donacionesasignaciones';
    protected $primaryKey = 'donacionasignacionid';
    public $timestamps = false;
    protected $fillable = ['donacionid','asignacionid','montoasignado','fechaasignacion'];

    public function donacion() {
        return $this->belongsTo(Donacion::class, 'donacionid', 'donacionid');
    }

    public function asignacion() {
        return $this->belongsTo(Asignacion::class, 'asignacionid', 'asignacionid');
    }
}
