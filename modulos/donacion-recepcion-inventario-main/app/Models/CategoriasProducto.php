<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriasProducto extends Model
{
    public const TIPOS_CATEGORIA = [
        'CONSUMO' => 'Consumo',
        'SALUD' => 'Salud',
        'HIGIENE' => 'Higiene',
        'VESTIMENTA' => 'Vestimenta',
        'HERRAMIENTA' => 'Herramientas',
        'REFUGIO' => 'Refugio',
        'RESCATE' => 'Rescate',
        'OTRO' => 'Otro',
    ];

    public const PRIORIDADES = [
        'alta' => 'Alta',
        'media' => 'Media',
        'baja' => 'Baja',
    ];

    public const UNIDADES_MEDIDA = [
        'litros' => 'Litros',
        'unidades' => 'Unidades',
        'cajas' => 'Cajas',
        'kilos' => 'Kilos',
        'paquetes' => 'Paquetes',
        'botellas' => 'Botellas',
    ];

    public const CONDICIONES_SUGERIDAS = [
        'Refrigerado',
        'Ambiente seco',
        'Protegido del sol',
        'Ambiente ventilado',
        'Temperatura controlada',
        'Elevado del piso',
    ];

    public const ESTADOS = [
        'activo' => 'Activa',
        'inactivo' => 'Inactiva',
    ];

    protected $connection = 'inventario';

    protected $perPage = 20;

    protected $primaryKey = 'id_categoria';

    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'tipo_categoria',
        'unidad_medida',
        'es_perecedero',
        'requiere_fecha_vencimiento',
        'prioridad',
        'condiciones_almacenamiento',
        'recomendaciones_uso',
        'observaciones',
        'color',
        'icono',
        'estado',
    ];

    protected $casts = [
        'es_perecedero' => 'boolean',
        'requiere_fecha_vencimiento' => 'boolean',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria', 'id_categoria');
    }

    public function historial()
    {
        return $this->hasMany(CategoriaProductoHistorial::class, 'id_categoria', 'id_categoria')
            ->orderByDesc('created_at');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeOrdenEmergencia($query)
    {
        return $query->orderByRaw("CASE prioridad WHEN 'alta' THEN 1 WHEN 'media' THEN 2 ELSE 3 END")
            ->orderBy('nombre');
    }

    public function esVestimenta(): bool
    {
        return $this->tipo_categoria === 'VESTIMENTA';
    }

    public function etiquetaPrioridad(): string
    {
        return self::PRIORIDADES[$this->prioridad] ?? ucfirst((string) $this->prioridad);
    }

    public function toDonacionMeta(): array
    {
        return [
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'tipo_categoria' => $this->tipo_categoria,
            'requiere_fecha_vencimiento' => (bool) $this->requiere_fecha_vencimiento,
            'es_perecedero' => (bool) $this->es_perecedero,
            'requiere_talla' => $this->esVestimenta(),
            'unidad_medida' => $this->unidad_medida,
            'prioridad' => $this->prioridad,
        ];
    }

    public function toProductoMeta(): array
    {
        return [
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'tipo_categoria' => $this->tipo_categoria,
            'tipo_label' => self::TIPOS_CATEGORIA[$this->tipo_categoria] ?? $this->tipo_categoria,
            'prioridad' => $this->prioridad,
            'prioridad_label' => $this->etiquetaPrioridad(),
            'requiere_fecha_vencimiento' => (bool) $this->requiere_fecha_vencimiento,
            'es_perecedero' => (bool) $this->es_perecedero,
            'requiere_talla' => $this->esVestimenta(),
            'producto_restringido' => $this->tipo_categoria === 'SALUD',
            'unidad_medida' => $this->unidad_medida,
            'condiciones_almacenamiento' => $this->condiciones_almacenamiento,
            'estado' => $this->estado,
        ];
    }

    public static function generarCodigo(string $nombre): string
    {
        $slug = strtoupper(preg_replace('/[^A-Z0-9]+/', '-', strtoupper($nombre)) ?? 'OTRO');
        $slug = trim($slug, '-');

        return 'CAT-'.substr($slug ?: 'OTRO', 0, 16);
    }
}
