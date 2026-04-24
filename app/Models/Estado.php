<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estados';
    protected $primaryKey = 'estadoid';
    public $timestamps = false;
    protected $fillable = ['nombre','descripcion'];

    public function donaciones() {
        return $this->hasMany(Donacion::class, 'estadoid', 'estadoid');
    }
}
