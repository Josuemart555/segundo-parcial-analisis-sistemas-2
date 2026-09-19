<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstadoCitaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'nombre' => $this->nombre,
            'color' => $this->color,
            'es_terminal' => $this->es_terminal,
            'bloquea_horario' => $this->bloquea_horario,
            'orden' => $this->orden,
        ];
    }
}
