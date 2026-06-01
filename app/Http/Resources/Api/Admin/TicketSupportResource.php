<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TicketSupport */
class TicketSupportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sujet' => $this->sujet,
            'message' => $this->message,
            'reponse_admin' => $this->reponse_admin,
            'reponse_admin_at' => $this->reponse_admin_at,
            'statut' => $this->statut,
            'paroisse' => new ParoisseResource($this->whenLoaded('paroisse')),
            'user_paroisse' => new UserParoisseResource($this->whenLoaded('userParoisse')),
            'created_at' => $this->created_at,
        ];
    }
}
