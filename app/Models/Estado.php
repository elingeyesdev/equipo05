<?php
namespace App\Models;
use App\Models\TransparenciaModel;

class Estado extends TransparenciaModel
{
    protected $table = 'estados';
    protected $primaryKey = 'estadoid';
    public $timestamps = false;
    protected $fillable = ['nombre','descripcion'];

    public function donaciones() {
        return $this->hasMany(Donacion::class, 'estadoid', 'estadoid');
    }
}
