<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Park extends Model implements HasMedia
{
    use UsesUuid, HasSlug, InteractsWithMedia;

    protected $fillable = [
        'city_id',
        'name',
        'slug',
        'description',
        'hero_image',
        'latitude',
        'longitude',
        'opening_hours',
        'price_adult',
        'price_child',
        'price_parking',
        'price_locker',
        'water_heated_areas',
        'shade_level',
        'family_index',
        'best_for',
        'not_for',
        'anti_queue_tips',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'price_adult' => 'decimal:2',
        'price_child' => 'decimal:2',
        'price_parking' => 'decimal:2',
        'price_locker' => 'decimal:2',
        'water_heated_areas' => 'integer',
        'family_index' => 'integer',
        'best_for' => 'array',
        'not_for' => 'array',
        'anti_queue_tips' => 'array',
        'is_active' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'park_tags');
    }

    public function attractions(): HasMany
    {
        return $this->hasMany(Attraction::class)->orderBy('display_order');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ParkPhoto::class)->orderBy('display_order');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(ParkVideo::class)->orderBy('display_order');
    }

    public function faq(): HasMany
    {
        return $this->hasMany(ParkFaq::class)->orderBy('display_order');
    }

    public function comfortPoints(): HasMany
    {
        return $this->hasMany(ComfortPoint::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class);
    }

    // Computed attributes
    public function getRatingAttribute(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    // Media Library Collections
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero')
            ->singleFile()
            ->useFallbackUrl('/images/park-placeholder.jpg');

        $this->addMediaCollection('gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->sharpen(10)
            ->nonQueued();

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(600)
            ->nonQueued();
    }
}
