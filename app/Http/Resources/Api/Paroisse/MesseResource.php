<?php

namespace App\Http\Resources\Api\Paroisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Messe */
class MesseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'modele_messe_id' => $this->modele_messe_id,
            'titre' => $this->titre,
            'description' => $this->description,
            'date' => $this->date,
            'heure' => $this->heure,
            'reservable' => $this->reservable,
            'capacite_max' => $this->capacite_max,
            'places_reservees' => $this->places_reservees,
            'visible' => $this->visible,
            'statut' => $this->statut,
            'modele_messe' => new ModeleMesseResource($this->whenLoaded('modeleMesse')),
            'demandes_count' => $this->whenCounted('demandes'),
            'demandes' => DemandeMesseResource::collection($this->whenLoaded('demandes')),
            'created_at' => $this->created_at,
        ];
    }
}
