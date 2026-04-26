<?php

namespace Modules\Rescate\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TreatmentType
 *
 * @property $id
 * @property $nombre
 * @property $created_at
 * @property $updated_at
 *
 * @property MedicalEvaluation[] $medicalEvaluations
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class TreatmentType extends Model
{
    
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
    public function medicalEvaluations()
    {
        return $this->hasMany(\Modules\Rescate\Models\MedicalEvaluation::class, 'id', 'tratamiento_id');
    }
    
}
