<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'state' => $this->whenLoaded('state', fn() => [
                'abbr' => $this->state->abbr,
                'name' => $this->state->name,
            ]),
            'image' => $this->image,
            'featured' => $this->featured,
            'parkCount' => $this->when(isset($this->parks_count), $this->parks_count),
        ];
    }
}
