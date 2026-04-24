<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'rolid';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class,
            'usuariosroles',
            'rolid',
            'usuarioid',
            'rolid',
            'usuarioid'
        );
    }
}
