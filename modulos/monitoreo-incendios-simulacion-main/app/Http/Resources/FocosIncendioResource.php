<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FocosIncendioResource extends JsonResource
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
            'fecha' => $this->fecha,
            'ubicacion' => $this->ubicacion,
            'coordenadas' => $this->coordenadas,
            'intensidad' => $this->intensidad,
            'estado' => $this->estado,
            'predictions' => PredictionResource::collection($this->whenLoaded('predictions')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
