<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// 1. Importamos el Trait de Spatie
use Spatie\Permission\Traits\HasRoles; 

class Usuario extends Authenticatable
{
    // 2. Usamos el Trait (esto agrega automáticamente hasRole, assignRole, etc.)
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'usuarios';
    protected $primaryKey = 'usuarioid'; // Spatie respetará tu ID personalizado automáticamente
    public $timestamps = false;

    protected $fillable = [
        'email', 'contrasena', 'nombre', 'apellido', 'telefono', 'imagenurl', 'activo', 'fecharegistro'
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    /**
     * CI para el módulo de incendios (columna ci_usuario / trazabilidad).
     * El modelo del core no tiene cedula_identidad: derivamos un valor estable.
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

        return 'CORE-' . (string) $this->getKey();
    }

    // NOTA: Borramos la función roles() manual y hasRole() manual. 
    // El Trait 'HasRoles' ya se encarga de todo eso internamente.

    // Relaciones adicionales de tu proyecto (se mantienen)
    public function campanias() { return $this->hasMany(Campania::class, 'usuarioidcreador', 'usuarioid'); }
    public function donaciones() { return $this->hasMany(Donacion::class, 'usuarioid', 'usuarioid'); }
    public function mensajesEnviados() { return $this->hasMany(Mensaje::class, 'usuarioorigen', 'usuarioid'); }
    public function mensajesRecibidos() { return $this->hasMany(Mensaje::class, 'usuariodestino', 'usuarioid'); }
}