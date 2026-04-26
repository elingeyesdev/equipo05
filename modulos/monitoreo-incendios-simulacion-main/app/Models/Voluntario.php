<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voluntario extends Model
{
    use HasFactory;

    protected $table = 'voluntarios';

    protected $fillable = [
        'user_id',
        'direccion',
        'ciudad',
        'zona',
        'notas',
    ];

    /**
     * Relación con el usuario base
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
