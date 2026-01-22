<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'heroImage' => $this->getFirstMediaUrl('hero') ?: $this->hero_image,
            'city' => $this->whenLoaded('city', fn() => [
                'id' => $this->city->id,
                'name' => $this->city->name,
                'state' => $this->city->state->abbr ?? null,
            ]),
            'priceAdult' => (float) $this->price_adult,
            'priceChild' => (float) $this->price_child,
            'familyIndex' => $this->family_index,
            'waterHeatedAreas' => $this->water_heated_areas,
            'shadeLevel' => $this->shade_level,
            'tags' => $this->whenLoaded(
                'tags',
                fn() =>
                $this->tags->pluck('slug')->toArray()
            ),
            'distanceKm' => $this->when(
                isset($this->distance_km),
                fn() => round($this->distance_km, 1)
            ),
        ];
    }
}
