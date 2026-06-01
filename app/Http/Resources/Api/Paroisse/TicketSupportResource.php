<?php

namespace App\Http\Resources\Api\Paroisse;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\TicketSupport */
class TicketSupportResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sujet' => $this->sujet,
            'message' => $this->message,
            'statut' => $this->statut,
            'user_paroisse' => new UserParoisseResource($this->whenLoaded('userParoisse')),
            'created_at' => $this->created_at,
        ];
    }
}
