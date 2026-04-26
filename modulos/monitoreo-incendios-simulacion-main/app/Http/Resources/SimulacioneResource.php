<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimulacioneResource extends JsonResource
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
            'nombre' => $this->nombre,
            'admin_id' => $this->admin_id,
            'administrador' => $this->whenLoaded('administrador'),
            'parameters' => $this->parameters,
            'initial_fires' => $this->initial_fires,
            'history' => $this->history,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
