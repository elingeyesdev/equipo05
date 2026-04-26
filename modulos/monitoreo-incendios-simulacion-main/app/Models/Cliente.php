<?php

namespace Modules\Incendios\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    protected $connection = 'incendios';
    use HasFactory;

    protected $table = 'clientes';

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
