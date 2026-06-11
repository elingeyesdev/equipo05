<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    public const PRIORIDADES = [
        'alta' => 'Alta',
        'media' => 'Media',
        'baja' => 'Baja',
    ];

    public const ESTADOS = [
        'activo' => 'Activo',
        'inactivo' => 'Inactivo',
        'restringido' => 'Restringido',
    ];

    protected $connection = 'inventario';

    protected $perPage = 20;

    protected $primaryKey = 'id_producto';

    public $timestamps = false;

    protected $fillable = [
        'id_categoria',
        'codigo',
        'nombre',
        'descripcion',
        'imagen_url',
        'unidad_medida',
        'prioridad',
        'estado',
        'requiere_vencimiento',
        'requiere_talla',
        'requiere_condicion',
        'producto_restringido',
        'stock_minimo',
        'condiciones_almacenamiento',
        'observaciones',
    ];

    protected $casts = [
        'requiere_vencimiento' => 'boolean',
        'requiere_talla' => 'boolean',
        'requiere_condicion' => 'boolean',
        'producto_restringido' => 'boolean',
        'stock_minimo' => 'integer',
    ];

    public function categoriaProducto()
    {
        return $this->belongsTo(CategoriasProducto::class, 'id_categoria', 'id_categoria');
    }

    public function categoriasProducto()
    {
        return $this->belongsTo(CategoriasProducto::class, 'id_categoria', 'id_categoria');
    }

    public function donacionDetalles()
    {
        return $this->hasMany(DonacionDetalle::class, 'id_producto', 'id_producto');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeOrdenPrioridad($query)
    {
        return $query->orderByRaw("CASE prioridad WHEN 'alta' THEN 1 WHEN 'media' THEN 2 ELSE 3 END")
            ->orderBy('nombre');
    }

    public function etiquetaPrioridad(): string
    {
        return self::PRIORIDADES[$this->prioridad] ?? ucfirst((string) $this->prioridad);
    }

    public function etiquetaEstado(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst((string) $this->estado);
    }

    public function badgePrioridad(): string
    {
        return match ($this->prioridad) {
            'alta' => 'danger',
            'media' => 'warning',
            default => 'secondary',
        };
    }

    public function badgeEstado(): string
    {
        return match ($this->estado) {
            'activo' => 'success',
            'restringido' => 'dark',
            default => 'secondary',
        };
    }

    public function tieneRegistrosAsociados(): bool
    {
        return $this->donacionDetalles()->exists();
    }

    public function informacionIncompleta(): bool
    {
        return empty($this->unidad_medida) || empty($this->codigo) || empty($this->id_categoria);
    }

    public static function generarCodigo(string $nombre): string
    {
        $slug = strtoupper(trim(preg_replace('/[^A-Z0-9]+/', '-', strtoupper($nombre)) ?: 'ITEM', '-'));
        $slug = substr($slug, 0, 30) ?: 'ITEM';

        return 'PROD-'.$slug;
    }

    public static function estadisticasCatalogo(): array
    {
        return [
            'total' => static::count(),
            'activos' => static::where('estado', 'activo')->count(),
            'inactivos' => static::where('estado', 'inactivo')->count(),
            'restringidos' => static::where('estado', 'restringido')->count(),
            'alta_prioridad' => static::where('prioridad', 'alta')->count(),
            'requieren_vencimiento' => static::where('requiere_vencimiento', true)->count(),
        ];
    }
}
