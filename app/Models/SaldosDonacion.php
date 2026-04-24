<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SaldosDonacion extends Model
{
    protected $table = 'saldosdonaciones';
    protected $primaryKey = 'saldoid';
    public $timestamps = false;
    protected $fillable = ['donacionid','montooriginal','montoutilizado','saldodisponible','ultimaactualizacion'];

    public function donacion() {
        return $this->belongsTo(Donacion::class, 'donacionid', 'donacionid');
    }
}
