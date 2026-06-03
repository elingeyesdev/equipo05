<?php

namespace App\Models;

use App\Support\UnifiedPostgres;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'usuarios';

    protected $primaryKey = 'usuarioid';

    public $timestamps = false;

    protected $fillable = [
        'email', 'contrasena', 'nombre', 'apellido', 'telefono', 'imagenurl', 'activo', 'fecharegistro', 'remember_token',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    public function getConnectionName(): ?string
    {
        return UnifiedPostgres::coreAuthConnection();
    }

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function getNameAttribute(): string
    {
        return trim((string) $this->nombre.' '.(string) $this->apellido);
    }

    public function getIdAttribute(): ?int
    {
        return $this->usuarioid;
    }

    /**
     * CI para el módulo de incendios (columna ci_usuario / trazabilidad).
     */
    public function getCedulaIdentidadAttribute(): ?string
    {
        if (array_key_exists('cedula_identidad', $this->attributes)) {
            $raw = $this->attributes['cedula_identidad'];
            if ($raw !== null && $raw !== '') {
                return (string) $raw;
            }
        }
        if (! empty($this->telefono)) {
            $digits = preg_replace('/\D+/', '', (string) $this->telefono);

            return $digits !== '' ? $digits : null;
        }

        return 'CORE-'.(string) $this->getKey();
    }

    public function campanias()
    {
        return $this->hasMany(Campania::class, 'usuarioidcreador', 'usuarioid');
    }

    public function donaciones()
    {
        return $this->hasMany(Donacion::class, 'usuarioid', 'usuarioid');
    }

    public function mensajesEnviados()
    {
        return $this->hasMany(Mensaje::class, 'usuarioorigen', 'usuarioid');
    }

    public function mensajesRecibidos()
    {
        return $this->hasMany(Mensaje::class, 'usuariodestino', 'usuarioid');
    }
}
