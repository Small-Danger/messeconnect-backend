<?php

namespace App\Http\Resources\Api\Paroisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ModeleMesse */
class ModeleMesseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'jour_semaine' => $this->jour_semaine,
            'heure' => $this->heure,
            'reservable' => $this->reservable,
            'capacite_max' => $this->capacite_max,
            'date_debut' => $this->date_debut,
            'date_fin' => $this->date_fin,
            'actif' => $this->actif,
            'messes_count' => $this->whenCounted('messes'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
