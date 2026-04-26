<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Administrador extends Model
{
    use HasFactory;

    protected $table = 'administradores';

    protected $fillable = [
        'user_id',
        'departamento',
        'nivel_acceso',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Relación con el usuario base
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Simulaciones creadas por este administrador
     */
    public function simulaciones()
    {
        return $this->hasMany(\App\Models\Simulacione::class, 'admin_id');
    }
}
