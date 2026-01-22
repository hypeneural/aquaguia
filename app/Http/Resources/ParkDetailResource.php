<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParkDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,

            'city' => $this->whenLoaded('city', fn() => [
                'id' => $this->city->id,
                'name' => $this->city->name,
                'slug' => $this->city->slug,
                'state' => [
                    'abbr' => $this->city->state->abbr,
                    'name' => $this->city->state->name,
                ],
            ]),

            'heroImage' => $this->getFirstMediaUrl('hero') ?: $this->hero_image,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'openingHours' => $this->opening_hours,

            'priceAdult' => (float) $this->price_adult,
            'priceChild' => (float) $this->price_child,
            'priceParking' => (float) $this->price_parking,
            'priceLocker' => (float) $this->price_locker,

            'waterHeatedAreas' => $this->water_heated_areas,
            'shadeLevel' => $this->shade_level,
            'familyIndex' => $this->family_index,

            'tags' => $this->whenLoaded(
                'tags',
                fn() =>
                $this->tags->pluck('slug')->toArray()
            ),

            'bestFor' => $this->best_for,
            'notFor' => $this->not_for,
            'antiQueueTips' => $this->anti_queue_tips,

            'photos' => $this->whenLoaded(
                'photos',
                fn() =>
                $this->photos->map(fn($photo) => [
                    'id' => $photo->id,
                    'url' => $photo->url,
                    'caption' => $photo->caption,
                    'order' => $photo->display_order,
                ])
            ),

            'videos' => $this->whenLoaded(
                'videos',
                fn() =>
                $this->videos->map(fn($video) => [
                    'id' => $video->id,
                    'youtubeId' => $video->youtube_id,
                    'title' => $video->title,
                    'order' => $video->display_order,
                ])
            ),

            'attractions' => $this->whenLoaded(
                'attractions',
                fn() =>
                AttractionResource::collection($this->attractions)
            ),

            'comfortPoints' => $this->whenLoaded(
                'comfortPoints',
                fn() =>
                $this->comfortPoints->map(fn($point) => [
                    'id' => $point->id,
                    'type' => $point->type,
                    'label' => $point->label,
                    'x' => (float) $point->x,
                    'y' => (float) $point->y,
                ])
            ),

            'faq' => $this->whenLoaded(
                'faq',
                fn() =>
                $this->faq->map(fn($item) => [
                    'id' => $item->id,
                    'question' => $item->question,
                    'answer' => $item->answer,
                    'order' => $item->display_order,
                ])
            ),

            'rating' => $this->rating,
            'reviewCount' => $this->review_count,

            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
