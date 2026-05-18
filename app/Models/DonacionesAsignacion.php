<?php
namespace App\Models;
use App\Models\TransparenciaModel;

class DonacionesAsignacion extends TransparenciaModel
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
