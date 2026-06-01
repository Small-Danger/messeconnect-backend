<?php

namespace App\Http\Resources\Api\Fidele;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\MediaParoisse */
class MediaParoisseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'url' => $this->url,
            'ordre' => $this->ordre,
        ];
    }
}
