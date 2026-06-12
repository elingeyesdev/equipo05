<?php

namespace Modules\Rescate\Models;

use App\Support\UnifiedPostgres;
use App\Support\UnifiedValidation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = ['email', 'password', 'contrasena', 'nombre', 'apellido', 'activo', 'fecharegistro', 'name'];

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
        return self::relationKey();
    }

    public static function relationKey(): string
    {
        return UnifiedPostgres::enabled() ? 'usuarioid' : 'id';
    }

    /**
     * IDs de usuarios que solo tienen rol ciudadano (excluidos de listas operativas).
     *
     * @return \Illuminate\Support\Collection<int, int>
     */
    public static function onlyCitizenUserIds(): \Illuminate\Support\Collection
    {
        $key = self::relationKey();

        return static::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['Ciudadano', 'ciudadano']))
            ->whereDoesntHave('roles', fn ($query) => $query->whereIn('name', [
                'Administrador', 'admin', 'administrador',
                'Operador de Incendios', 'encargado',
                'Rescatista', 'rescatista',
                'Veterinario', 'veterinario',
                'Cuidador', 'cuidador',
            ]))
            ->pluck($key);
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
        if ($value === null || $value === '') {
            return;
        }

        if (UnifiedPostgres::enabled()) {
            $this->attributes['contrasena'] = is_string($value) && str_starts_with($value, '$2y$')
                ? $value
                : Hash::make($value);

            return;
        }

        $this->attributes['password'] = $value;
    }

    public function setNameAttribute($value): void
    {
        if (UnifiedPostgres::enabled()) {
            $parts = UnifiedValidation::splitNombreCompleto((string) $value);
            $this->attributes['nombre'] = $parts['nombre'];
            $this->attributes['apellido'] = $parts['apellido'];

            return;
        }

        $this->attributes['name'] = $value;
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
