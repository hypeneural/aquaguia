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

            // Address (NEW)
            'address' => [
                'street' => $this->address_street,
                'neighborhood' => $this->address_neighborhood,
                'city' => $this->city?->name,
                'state' => $this->city?->state?->abbr,
                'zip_code' => $this->address_zip_code,
                'country' => 'Brasil',
            ],

            // Website (NEW)
            'website' => $this->website,

            // Contact (NEW)
            'contact' => [
                'phone' => $this->contact_phone,
                'whatsapp' => $this->contact_whatsapp,
                'whatsapp_message' => $this->contact_whatsapp_message,
                'email' => $this->contact_email,
            ],

            // Social Links (NEW)
            'social_links' => [
                'instagram' => $this->social_instagram,
                'instagram_url' => $this->social_instagram_url,
                'facebook_url' => $this->social_facebook_url,
                'youtube_url' => $this->social_youtube_url,
                'tiktok_url' => $this->social_tiktok_url,
                'twitter_url' => $this->social_twitter_url,
            ],

            // Booking (NEW)
            'booking' => [
                'url' => $this->booking_url,
                'is_external' => $this->booking_is_external,
                'partner_name' => $this->booking_partner_name,
                'affiliate_code' => $this->booking_affiliate_code,
            ],

            'openingHours' => $this->opening_hours,

            // Pricing (EXTENDED)
            'pricing' => [
                'adult' => (float) $this->price_adult,
                'child' => (float) $this->price_child,
                'senior' => $this->price_senior ? (float) $this->price_senior : null,
                'child_free_under' => $this->price_child_free_under,
                'senior_age_from' => $this->price_senior_age_from,
                'parking' => (float) $this->price_parking,
                'locker' => (float) $this->price_locker,
                'locker_small' => $this->price_locker_small ? (float) $this->price_locker_small : null,
                'locker_large' => $this->price_locker_large ? (float) $this->price_locker_large : null,
                'locker_family' => $this->price_locker_family ? (float) $this->price_locker_family : null,
                'vip_cabana' => $this->price_vip_cabana ? (float) $this->price_vip_cabana : null,
                'all_inclusive' => $this->price_all_inclusive ? (float) $this->price_all_inclusive : null,
                'currency' => 'BRL',
                'valid_until' => $this->price_valid_until?->format('Y-m-d'),
            ],

            // Keep legacy fields for backwards compatibility
            'priceAdult' => (float) $this->price_adult,
            'priceChild' => (float) $this->price_child,
            'priceParking' => (float) $this->price_parking,
            'priceLocker' => (float) $this->price_locker,

            'waterHeatedAreas' => $this->water_heated_areas,
            'shadeLevel' => $this->shade_level,
            'familyIndex' => $this->family_index,

            'tags' => $this->relationLoaded('tags')
                ? $this->tags->pluck('slug')->toArray()
                : [],

            'bestFor' => $this->best_for,
            'notFor' => $this->not_for,
            'antiQueueTips' => $this->anti_queue_tips,

            'photos' => $this->whenLoaded(
                'photos',
                fn() =>
                $this->photos->map(fn($photo) => [
                    'id' => $photo->id,
                    'url' => $photo->url,
                    'thumbnail_url' => $photo->thumbnail_url ?? $photo->url,
                    'caption' => $photo->caption,
                    'order' => $photo->display_order,
                    'is_hero' => $photo->display_order === 0,
                ])
            ),

            'videos' => $this->whenLoaded(
                'videos',
                fn() =>
                $this->videos->map(fn($video) => [
                    'id' => $video->id,
                    'youtube_id' => $video->youtube_id,
                    'title' => $video->title,
                    'order' => $video->display_order,
                    'duration_seconds' => $video->duration_seconds ?? null,
                    'thumbnail_url' => $video->thumbnail_url ?? "https://img.youtube.com/vi/{$video->youtube_id}/hqdefault.jpg",
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

            'rating' => (float) ($this->rating ?? 0),
            'reviewCount' => (int) ($this->review_count ?? 0),

            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
