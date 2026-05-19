<?php

namespace Modules\Incendios\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FocoIncendio extends Model
{
    protected $connection = 'incendios';
    use HasFactory;

    protected $table = 'focos_incendios';

    protected $fillable = [
        'fecha',
        'ubicacion',
        'coordenadas', // array [lat, lng] or object stored as JSON
        'intensidad',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'coordenadas' => 'array',
        'intensidad' => 'float',
    ];

    // You can add helpers to get latitude/longitude convenience accessors
    /**
     * The simulation this foco belongs to (nullable).
     */
    public function simulacion()
    {
        return $this->belongsTo(Simulacion::class, 'simulacion_id');
    }

    /**
     * The biomasa (area) related to this foco (nullable)
     */
    public function biomasa()
    {
        return $this->belongsTo(Biomasa::class, 'biomasa_id');
    }

    /**
     * User that reported the foco
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by', User::relationKey());
    }

    /**
     * Movement / intensity tracks for this foco
     */
    public function tracks()
    {
        return $this->hasMany(FocoTrack::class, 'foco_incendio_id');
    }

    /**
     * Get latitude from coordenadas array (handles different formats)
     */
    public function getLatitudeAttribute()
    {
        if (!$this->coordenadas) return null;
        
        if (is_array($this->coordenadas)) {
            return $this->coordenadas['lat'] ?? $this->coordenadas['latitude'] ?? $this->coordenadas[0] ?? null;
        }
        
        return null;
    }

    /**
     * Get longitude from coordenadas array (handles different formats)
     */
    public function getLongitudeAttribute()
    {
        if (!$this->coordenadas) return null;
        
        if (is_array($this->coordenadas)) {
            return $this->coordenadas['lng'] ?? $this->coordenadas['lon'] ?? $this->coordenadas['longitude'] ?? $this->coordenadas[1] ?? null;
        }
        
        return null;
    }

    /**
     * Get formatted coordinates string
     */
    public function getFormattedCoordinatesAttribute()
    {
        $lat = $this->latitude;
        $lng = $this->longitude;
        
        if ($lat && $lng) {
            return number_format($lat, 6) . ', ' . number_format($lng, 6);
        }
        
        return 'N/A';
    }
}
