<?php

namespace App\Http\Resources\Api\Fidele;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Notification */
class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'statut' => $this->statut,
            'demande_messe_id' => $this->demande_messe_id,
            'date_envoi' => $this->date_envoi,
            'created_at' => $this->created_at,
        ];
    }
}
