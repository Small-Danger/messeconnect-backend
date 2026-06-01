<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\CampagneCollecte */
class CampagneCollecteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'objectif_total' => $this->objectif_total,
            'montant_collecte' => $this->montant_collecte,
            'date_fin' => $this->date_fin,
            'paroisse' => new ParoisseResource($this->whenLoaded('paroisse')),
            'created_at' => $this->created_at,
        ];
    }
}
