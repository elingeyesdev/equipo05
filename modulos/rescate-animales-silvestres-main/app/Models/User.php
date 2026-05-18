<?php

namespace Modules\Rescate\Models;

use App\Support\UnifiedPostgres;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = ['email', 'password', 'contrasena', 'nombre', 'apellido'];

    protected $hidden = ['password', 'contrasena', 'remember_token'];

    public function getConnectionName(): ?string
    {
        return UnifiedPostgres::enabled() ? UnifiedPostgres::coreAuthConnection() : 'rescate';
    }

    public function getTable(): string
    {
        return UnifiedPostgres::enabled() ? 'usuarios' : 'users';
    }

    public function getKeyName(): string
    {
        return UnifiedPostgres::enabled() ? 'usuarioid' : 'id';
    }

    protected function casts(): array
    {
        if (UnifiedPostgres::enabled()) {
            return [];
        }

        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return UnifiedPostgres::enabled() ? $this->contrasena : $this->password;
    }

    public function setPasswordAttribute($value): void
    {
        if (UnifiedPostgres::enabled()) {
            $this->attributes['contrasena'] = $value;

            return;
        }

        $this->attributes['password'] = $value;
    }

    public function getIdAttribute(): ?int
    {
        return UnifiedPostgres::enabled()
            ? ($this->attributes['usuarioid'] ?? null)
            : ($this->attributes['id'] ?? null);
    }

    public function person()
    {
        return $this->hasOne(Person::class, 'usuario_id', $this->getKeyName());
    }

    public function getNameAttribute(): string
    {
        if ($this->relationLoaded('person') || $this->person) {
            return (string) ($this->person->nombre ?? '');
        }

        if (UnifiedPostgres::enabled()) {
            return trim((string) ($this->attributes['nombre'] ?? '').' '.(string) ($this->attributes['apellido'] ?? ''));
        }

        return (string) ($this->attributes['email'] ?? '');
    }

    public function adminlte_image()
    {
        if ($this->person && $this->person->foto_path) {
            return asset('storage/'.$this->person->foto_path);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    public function adminlte_profile_url()
    {
        return 'profile';
    }

    public function adminlte_desc(): ?string
    {
        if (! method_exists($this, 'getRoleNames')) {
            return null;
        }

        $roles = $this->getRoleNames();
        if ($roles->isEmpty()) {
            return null;
        }

        return $roles->map(fn ($r) => ucfirst($r))->implode(', ');
    }
}
