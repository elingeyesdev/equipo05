<?php

namespace Modules\Incendios\Models;

use App\Support\UnifiedPostgres;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $perPage = 20;

    public function getConnectionName(): ?string
    {
        return UnifiedPostgres::enabled() ? UnifiedPostgres::coreAuthConnection() : 'incendios';
    }

    public function getTable(): string
    {
        return UnifiedPostgres::enabled() ? 'usuarios' : 'users';
    }

    public function getKeyName(): string
    {
        return UnifiedPostgres::enabled() ? 'usuarioid' : 'id';
    }

    protected $fillable = ['name', 'email', 'telefono', 'cedula_identidad', 'password', 'google_id', 'nombre', 'apellido', 'contrasena'];

    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        if (UnifiedPostgres::enabled()) {
            return $this->contrasena;
        }

        return $this->password;
    }

    public function getNameAttribute(): string
    {
        if (UnifiedPostgres::enabled()) {
            return trim((string) ($this->attributes['nombre'] ?? '').' '.(string) ($this->attributes['apellido'] ?? ''));
        }

        return (string) ($this->attributes['name'] ?? '');
    }

    public function getIdAttribute(): ?int
    {
        return UnifiedPostgres::enabled()
            ? ($this->attributes['usuarioid'] ?? null)
            : ($this->attributes['id'] ?? null);
    }

    public function biomasas()
    {
        return $this->hasMany(Biomasa::class, 'user_id', $this->getKeyName());
    }

    public function voluntario()
    {
        return $this->hasOne(Voluntario::class, 'user_id', $this->getKeyName());
    }

    public function administrador()
    {
        return $this->hasOne(Administrador::class, 'user_id', $this->getKeyName());
    }

    public function isVoluntario()
    {
        return $this->hasRole('voluntario') || $this->voluntario()->exists();
    }

    public function isAdministrador()
    {
        return $this->hasRole('administrador') || $this->administrador()->exists();
    }

    public function getRoleType()
    {
        if ($this->hasRole('administrador')) {
            return 'administrador';
        }
        if ($this->hasRole('voluntario')) {
            return 'voluntario';
        }

        if ($this->administrador()->exists()) {
            return 'administrador';
        }
        if ($this->voluntario()->exists()) {
            return 'voluntario';
        }

        return 'user';
    }

    public function adminlte_image()
    {
        $hash = md5(strtolower(trim((string) $this->email)));

        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=160";
    }

    public function adminlte_desc()
    {
        $role = $this->getRoleType();
        $roleLabel = match ($role) {
            'administrador' => 'Administrador',
            'voluntario' => 'Voluntario',
            default => 'Usuario',
        };

        $since = $this->created_at ?? $this->fecharegistro ?? now();

        return "{$roleLabel} • Miembro desde ".$since->format('M Y');
    }
}
