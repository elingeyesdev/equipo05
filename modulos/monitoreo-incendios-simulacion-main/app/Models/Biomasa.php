<?php

namespace Modules\Incendios\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

/**
 * Class Biomasa
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @mixin Builder
 */
class Biomasa extends Model
{
    protected $connection = 'incendios';
    use SoftDeletes;
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fecha_reporte',
        'tipo_biomasa_id',
        'area_m2',
        'perimetro_m',
        'densidad',
        'ubicacion',
        'coordenadas',
        'descripcion',
        'user_id',
        'ci_usuario',
        'estado',
        'motivo_rechazo',
        'aprobada_por',
        'fecha_revision',
    ];

    protected $casts = [
        'area_m2' => 'float',
        'perimetro_m' => 'float',
        // NO usar cast 'array' - el accessor se encarga de la conversión
        'fecha_reporte' => 'date',
        'fecha_revision' => 'datetime',
    ];
    
    /**
     * Accessor para asegurar que coordenadas siempre se devuelvan como array
     * Lee el valor crudo de la base de datos y lo convierte
     */
    public function getCoordenadasAttribute(): array
    {
        // Obtener el valor crudo directamente de attributes
        $value = $this->attributes['coordenadas'] ?? null;
        
        // Si el valor es null, retornar array vacío
        if ($value === null || $value === '') {
            return [];
        }
        
        // Si es un string, intentar parsearlo como JSON
        if (is_string($value)) {
            try {
                $decoded = json_decode($value, true);
                
                // Verificar si tenemos un string (doble encoding)
                if (is_string($decoded)) {
                    $decoded = json_decode($decoded, true);
                }
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return $decoded;
                }
                Log::warning("Error decodificando coordenadas de biomasa {$this->id}: " . json_last_error_msg());
                return [];
            } catch (Exception $e) {
                Log::error("Exception decodificando coordenadas de biomasa {$this->id}: " . $e->getMessage());
                return [];
            }
        }
        
        // Si ya es un array, retornarlo
        if (is_array($value)) {
            return $value;
        }
        
        return [];
    }
    
    /**
     * Mutator para convertir coordenadas a JSON antes de guardar
     */
    public function setCoordenadasAttribute(array|string|null $value): void
    {
        // Si ya es un string JSON, guardarlo directamente
        if (is_string($value)) {
            // Verificar si es un JSON válido
            json_decode($value);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->attributes['coordenadas'] = $value;
                return;
            }
        }
        
        // Si es un array, convertirlo a JSON
        if (is_array($value)) {
            $this->attributes['coordenadas'] = json_encode($value);
            return;
        }
        
        // Si es null o vacío
        $this->attributes['coordenadas'] = null;
    }
    
    /**
     * Scopes para filtrar por estado
     */
    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('estado', 'pendiente');
    }
    
    public function scopeAprobadas(Builder $query): Builder
    {
        return $query->where('estado', 'aprobada');
    }
    
    public function scopeRechazadas(Builder $query): Builder
    {
        return $query->where('estado', 'rechazada');
    }
    
    /**
     * Tipo de biomasa (catálogo)
     */
    public function tipoBiomasa()
    {
        return $this->belongsTo(\Modules\Incendios\Models\TipoBiomasa::class, 'tipo_biomasa_id');
    }
    
    /**
     * Usuario que creó esta biomasa (cualquiera puede crear)
     */
    public function user()
    {
        return $this->belongsTo(\Modules\Incendios\Models\User::class, 'user_id', User::relationKey());
    }
    
    /**
     * Administrador que aprobó/rechazó la biomasa
     */
    public function aprobadaPor()
    {
        return $this->belongsTo(\Modules\Incendios\Models\User::class, 'aprobada_por', User::relationKey());
    }
    
    /**
     * Verificar si está aprobada
     */
    public function estaAprobada()
    {
        return $this->estado === 'aprobada';
    }
    
    /**
     * Verificar si está pendiente
     */
    public function estaPendiente()
    {
        return $this->estado === 'pendiente';
    }
    
    /**
     * Verificar si está rechazada
     */
    public function estaRechazada()
    {
        return $this->estado === 'rechazada';
    }
}
