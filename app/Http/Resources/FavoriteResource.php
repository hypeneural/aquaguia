<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'parkId' => $this->park_id,
            'park' => $this->whenLoaded('park', fn() => new ParkListResource($this->park)),
            'addedAt' => $this->created_at?->toISOString(),
        ];
    }
}
