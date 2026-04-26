<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoBiomasaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo_biomasa' => $this->tipo_biomasa,
            'color' => $this->color,
            'modificador_intensidad' => $this->modificador_intensidad,
            'biomasas_count' => $this->when(isset($this->biomasas_count), $this->biomasas_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
