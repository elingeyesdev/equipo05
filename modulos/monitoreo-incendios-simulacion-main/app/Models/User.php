<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Simulacione;
use App\Models\FocosIncendio;
use App\Models\Voluntario;
use App\Models\Administrador;

/**
 * Class User
 *
 * @property $id
 * @property $name
 * @property $email
 * @property $email_verified_at
 * @property $password
 * @property $remember_token
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'telefono', 'cedula_identidad', 'password', 'google_id'];

    protected $casts = [
        'password' => 'hashed',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Biomasas created by this user (anyone can create)
     */
    public function biomasas()
    {
        return $this->hasMany(\App\Models\Biomasa::class, 'user_id');
    }

    /**
     * Voluntario profile for this user
     */
    public function voluntario()
    {
        return $this->hasOne(Voluntario::class);
    }

    /**
     * Administrador profile for this user
     */
    public function administrador()
    {
        return $this->hasOne(Administrador::class);
    }

    /**
     * Check if user is a voluntario
     * Uses Spatie roles with fallback to legacy table
     */
    public function isVoluntario()
    {
        return $this->hasRole('voluntario') || $this->voluntario()->exists();
    }

    /**
     * Check if user is an administrador
     * Uses Spatie roles with fallback to legacy table
     */
    public function isAdministrador()
    {
        return $this->hasRole('administrador') || $this->administrador()->exists();
    }

    /**
     * Get user role type
     * Uses Spatie roles with fallback to legacy tables
     */
    public function getRoleType()
    {
        // Check Spatie roles first
        if ($this->hasRole('administrador')) {
            return 'administrador';
        }
        if ($this->hasRole('voluntario')) {
            return 'voluntario';
        }
        
        // Fallback to legacy table check
        if ($this->administrador()->exists()) {
            return 'administrador';
        }
        if ($this->voluntario()->exists()) {
            return 'voluntario';
        }
        
        return 'user'; // base user without role extension
    }

    /**
     * Get the user's avatar image URL for AdminLTE
     * Returns a Gravatar or default avatar
     */
    public function adminlte_image()
    {
        // Opción 1: Usar Gravatar basado en email
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=160";
        
        // Opción 2: Usar un avatar predeterminado
        // return asset('vendor/adminlte/dist/img/avatar5.png');
    }

    /**
     * Get the user's description for AdminLTE header
     * Shows role and registration date
     */
    public function adminlte_desc()
    {
        $role = $this->getRoleType();
        $roleLabel = match($role) {
            'administrador' => 'Administrador',
            'voluntario' => 'Voluntario',
            default => 'Usuario'
        };
        
        return "{$roleLabel} • Miembro desde " . $this->created_at->format('M Y');
    }


}
