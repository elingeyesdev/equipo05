<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PredictionResource extends JsonResource
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
            'foco_incendio_id' => $this->foco_incendio_id,
            'focoIncendio' => $this->whenLoaded('focoIncendio'),
            'predicted_at' => $this->predicted_at,
            'path' => $this->path,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
