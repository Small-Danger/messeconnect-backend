<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Publication */
class PublicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'type' => $this->type,
            'image' => $this->image,
            'visible' => $this->visible,
            'date_publication' => $this->date_publication,
            'date_expiration' => $this->date_expiration,
            'paroisse' => new ParoisseResource($this->whenLoaded('paroisse')),
            'created_at' => $this->created_at,
        ];
    }
}
