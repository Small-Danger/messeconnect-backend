<?php

namespace App\Http\Resources\Api\Fidele;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Messe */
class MesseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'date' => $this->date,
            'heure' => $this->heure,
            'reservable' => $this->reservable,
            'capacite_max' => $this->capacite_max,
            'places_reservees' => $this->places_reservees,
            'places_disponibles' => $this->capacite_max !== null
                ? max(0, $this->capacite_max - $this->places_reservees)
                : null,
            'statut' => $this->statut,
        ];
    }
}
