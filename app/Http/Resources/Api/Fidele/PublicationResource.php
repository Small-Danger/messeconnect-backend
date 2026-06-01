<?php

namespace App\Http\Resources\Api\Fidele;

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
            'image' => $this->image,
            'images' => $this->images ?? ($this->image ? [$this->image] : []),
            'type' => $this->type,
            'date_publication' => $this->date_publication,
        ];
    }
}
