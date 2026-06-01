<?php

namespace App\Http\Resources\Api\Paroisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Publication */
class PublicationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'image' => $this->image,
            'images' => $this->images ?? ($this->image ? [$this->image] : []),
            'type' => $this->type,
            'date_publication' => $this->date_publication,
            'date_expiration' => $this->date_expiration,
            'visible' => $this->visible,
            'created_at' => $this->created_at,
        ];
    }
}
