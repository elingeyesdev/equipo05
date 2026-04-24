<?php

namespace Modules\Inventario\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class Usuario
 *
 * @property $id_usuario
 * @property $nombres
 * @property $apellidos
 * @property $ci
 * @property $foto_ci
 * @property $licencia_conducir
 * @property $foto_licencia
 * @property $genero
 * @property $correo
 * @property $telefono
 * @property $direccion_domicilio
 * @property $contrasena
 * @property $estado
 * @property $entidad_pertenencia
 * @property $tipo_sangre
 * @property $is_recolector
 * @property $remember_token
 * @property $fecha_registro
 *
 * @property Role $role
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Usuario extends Authenticatable
{
    protected $connection = 'inventario';
    use HasApiTokens, Notifiable, HasRoles;

    /** 👇 CLAVE: nombre de tabla y PK personalizada */
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;         // tu tabla usa fecha_registro, no created_at/updated_at
    public $incrementing = true;        // Laravel ya no pedirá el id manualmente
    protected $keyType = 'int';         // el id es entero
    protected $guard_name = 'web';      // Spatie solo para web, no API

    /** Paginación por defecto */
    protected $perPage = 20;

    /**
     * Campos que se pueden llenar en masa (mass assignable)
     */
    protected $fillable = [
        'nombres',
        'apellidos',
        'ci',
        'foto_ci',
        'licencia_conducir',
        'foto_licencia',
        'genero',
        'correo',
        'telefono',
        'direccion_domicilio',
        'contrasena',
        'estado',
        'entidad_pertenencia',
        'tipo_sangre',
        'is_recolector',
        'fecha_registro'
    ];

    /**
     * Campos ocultos en serialización JSON
     */
    protected $hidden = ['contrasena', 'remember_token'];

    /**
     * Método requerido por Authenticatable para obtener la contraseña
     * Laravel busca 'password' por defecto, le decimos que use 'contrasena'
     */
    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    /**
     * Get the name of the unique identifier for the user.
     * Retorna el nombre de la columna del ID (no del email/correo)
     */
    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    /**
     * Get the unique identifier for the user.
     * Retorna el valor del ID del usuario
     */
    public function getAuthIdentifier()
    {
        return $this->id_usuario;
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id_usuario';
    }

    /**
     * Get the user's name for display (used by AdminLTE).
     * Combines nombres and apellidos.
     */
    public function getNameAttribute()
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }

    /**
     * Get the user's email for display (used by AdminLTE).
     */
    public function getEmailAttribute()
    {
        return $this->correo;
    }

    public function getPrimaryRoleNameAttribute(): ?string
    {
        return DB::connection('inventario')
            ->table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', self::class)
            ->where('model_has_roles.model_id', $this->id_usuario)
            ->value('roles.name');
    }
}






