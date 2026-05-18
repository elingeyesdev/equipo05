<?php
namespace App\Models;
use App\Models\TransparenciaModel;

class SaldosDonacion extends TransparenciaModel
{
    protected $table = 'saldosdonaciones';
    protected $primaryKey = 'saldoid';
    public $timestamps = false;
    protected $fillable = ['donacionid','montooriginal','montoutilizado','saldodisponible','ultimaactualizacion'];

    public function donacion() {
        return $this->belongsTo(Donacion::class, 'donacionid', 'donacionid');
    }
}
