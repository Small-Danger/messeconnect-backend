<?php

namespace App\Http\Resources\Api\Paroisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\CampagneCollecte */
class CampagneCollecteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'description' => $this->description,
            'objectif_total' => $this->objectif_total,
            'montant_collecte' => $this->montant_collecte,
            'progression' => $this->objectif_total > 0
                ? round(($this->montant_collecte / $this->objectif_total) * 100, 2)
                : 0,
            'image' => $this->image,
            'date_fin' => $this->date_fin,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
