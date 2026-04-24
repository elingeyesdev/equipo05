<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donacion extends Model
{
    protected $table = 'donaciones';
    protected $primaryKey = 'donacionid';
    public $timestamps = false;

    protected $fillable = [
        'idexterno',
        'usuarioid',
        'campaniaid',
        'monto',
        'tipodonacion',
        'descripcion',
        'fechadonacion',
        'estadoid',
        'esanonima',
    ];

    protected $casts = [
        'esanonima'     => 'boolean',         // <- útil para checkbox
        'fechadonacion' => 'datetime',
        'monto'         => 'decimal:2',
    ];

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'usuarioid', 'usuarioid');
    }

    public function campania() {
        return $this->belongsTo(Campania::class, 'campaniaid', 'campaniaid');
    }

    public function estado() {
        return $this->belongsTo(Estado::class, 'estadoid', 'estadoid');
    }

    public function asignacionesPivot() {
        return $this->hasMany(DonacionesAsignacion::class, 'donacionid', 'donacionid');
    }

    public function saldo() {
        return $this->hasOne(SaldosDonacion::class, 'donacionid', 'donacionid');
    }
}
