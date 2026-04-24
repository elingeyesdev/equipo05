<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ext\ExtProducto;
use App\Models\Ext\ExtAlmacen;
use App\Models\Ext\ExtEstante;
use App\Models\Ext\ExtEspacio;

class TrazabilidadItem extends Model
{
    protected $table = 'trazabilidad_items';
    protected $primaryKey = 'trazabilidadid';
    public $timestamps = true;

    protected $fillable = [
        'campaniaid',
        'id_campana_externa',
        'campania_nombre',
        'codigo_unico',
        'id_donacion_externa',
        'id_detalle_externo',
        'productoid',
        'nombre_producto',
        'categoria_producto',
        'talla',
        'genero',
        'cantidad_donada',
        'cantidad_por_unidad',
        'unidad_empaque',
        'cantidad_ubicada',
        'cantidad_usada',
        'fecha_donacion',
        'tipo_donacion',
        'nombre_donante',
        'almacenid',
        'estanteid',
        'espacioid',
        'almacen_nombre',
        'estante_codigo',
        'espacio_codigo',
        'fecha_ingreso_almacen',
        'id_paquete_externo',
        'codigo_paquete',
        'estado_paquete',
        'fecha_creacion_paquete',
        'id_solicitud_externa',
        'codigo_solicitud',
        'estado_solicitud',
        'fecha_solicitud',
        'id_salida_externa',
        'destino_final',
        'fecha_salida',
        'estado_actual',
        'ubicacion_actual',
        'fecha_ultima_actualizacion',
    ];

    public function campania()
    {
        return $this->belongsTo(Campania::class, 'campaniaid', 'campaniaid');
    }

    public function producto()
    {
        return $this->belongsTo(ExtProducto::class, 'productoid', 'productoid');
    }

    public function almacen()
    {
        return $this->belongsTo(ExtAlmacen::class, 'almacenid', 'almacenid');
    }

    public function estante()
    {
        return $this->belongsTo(ExtEstante::class, 'estanteid', 'estanteid');
    }

    public function espacio()
    {
        return $this->belongsTo(ExtEspacio::class, 'espacioid', 'espacioid');
    }
}
