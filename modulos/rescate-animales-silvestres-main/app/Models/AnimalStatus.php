<?php

namespace Modules\Rescate\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AnimalStatus
 *
 * @property $id
 * @property $nombre
 * @property $created_at
 * @property $updated_at
 *
 * @property AnimalFile[] $animalFiles
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class AnimalStatus extends Model
{
    protected $connection = 'rescate';
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function animalFiles()
    {
        return $this->hasMany(\Modules\Rescate\Models\AnimalFile::class, 'id', 'estado_id');
    }
    
}
