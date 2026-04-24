<?php

namespace App\Models\Ext;

use Illuminate\Database\Eloquent\Model;

class ExtEstante extends Model
{
    protected $table = 'ext_estantes';
    protected $primaryKey = 'estanteid';
    public $timestamps = true;

    protected $fillable = [
        'idexterno',
        'almacenid',
        'codigo_estante',
        'descripcion',
    ];

    public function almacen()
    {
        return $this->belongsTo(ExtAlmacen::class, 'almacenid', 'almacenid');
    }

    public function espacios()
    {
        return $this->hasMany(ExtEspacio::class, 'estanteid', 'estanteid');
    }
}
