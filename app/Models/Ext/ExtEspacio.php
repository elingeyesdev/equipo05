<?php

namespace App\Models\Ext;

use Illuminate\Database\Eloquent\Model;

class ExtEspacio extends Model
{
    protected $table = 'ext_espacios';
    protected $primaryKey = 'espacioid';
    public $timestamps = true;

    protected $fillable = [
        'idexterno',
        'estanteid',
        'codigo_espacio',
        'estado',
    ];

    public function estante()
    {
        return $this->belongsTo(ExtEstante::class, 'estanteid', 'estanteid');
    }
    // Relación para ver qué items de trazabilidad están en este espacio
    public function items()
    {
        // 'espacioid' en TrazabilidadItem referencia a 'espacioid' en ExtEspacio
        return $this->hasMany(\App\Models\TrazabilidadItem::class, 'espacioid', 'espacioid')
                    ->where('estado_actual', 'En almacén'); // Opcional: solo mostrar lo que sigue ahí físicamente
    }
}
