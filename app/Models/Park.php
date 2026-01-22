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
        // Address
        'address_street',
        'address_neighborhood',
        'address_zip_code',
        // Website
        'website',
        // Contact
        'contact_phone',
        'contact_whatsapp',
        'contact_whatsapp_message',
        'contact_email',
        // Social media
        'social_instagram',
        'social_instagram_url',
        'social_facebook_url',
        'social_youtube_url',
        'social_tiktok_url',
        'social_twitter_url',
        // Booking
        'booking_url',
        'booking_is_external',
        'booking_partner_name',
        'booking_affiliate_code',
        // Operating hours
        'opening_hours',
        // Pricing
        'price_adult',
        'price_child',
        'price_senior',
        'price_child_free_under',
        'price_senior_age_from',
        'price_parking',
        'price_locker',
        'price_locker_small',
        'price_locker_large',
        'price_locker_family',
        'price_vip_cabana',
        'price_all_inclusive',
        'price_valid_until',
        // Classification
        'water_heated_areas',
        'shade_level',
        'family_index',
        'rating',
        'review_count',
        // Content
        'best_for',
        'not_for',
        'anti_queue_tips',
        // Extras (NEW)
        'accessibility_features',
        'payment_methods',
        'languages_spoken',
        'food_options',
        'prohibited_items',
        'what_to_bring',
        'minimum_stay_hours',
        'reservation_required',
        'max_capacity',
        'arrival_recommendation',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'price_adult' => 'decimal:2',
        'price_child' => 'decimal:2',
        'price_senior' => 'decimal:2',
        'price_parking' => 'decimal:2',
        'price_locker' => 'decimal:2',
        'price_locker_small' => 'decimal:2',
        'price_locker_large' => 'decimal:2',
        'price_locker_family' => 'decimal:2',
        'price_vip_cabana' => 'decimal:2',
        'price_all_inclusive' => 'decimal:2',
        'price_valid_until' => 'date',
        'price_child_free_under' => 'integer',
        'price_senior_age_from' => 'integer',
        'water_heated_areas' => 'integer',
        'family_index' => 'integer',
        'rating' => 'decimal:1',
        'review_count' => 'integer',
        'best_for' => 'array',
        'not_for' => 'array',
        'anti_queue_tips' => 'array',
        'accessibility_features' => 'array',
        'payment_methods' => 'array',
        'languages_spoken' => 'array',
        'food_options' => 'array',
        'prohibited_items' => 'array',
        'what_to_bring' => 'array',
        'minimum_stay_hours' => 'integer',
        'reservation_required' => 'boolean',
        'max_capacity' => 'integer',
        'is_active' => 'boolean',
        'booking_is_external' => 'boolean',
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

    public function operatingHours(): HasMany
    {
        return $this->hasMany(OperatingHour::class)->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')");
    }

    public function specialHours(): HasMany
    {
        return $this->hasMany(SpecialHour::class)->orderBy('start_date');
    }

    /**
     * Check if park is open now
     */
    public function isOpenNow(): bool
    {
        $now = now();
        $dayOfWeek = strtolower($now->englishDayOfWeek);

        // Check special hours first
        $specialHour = $this->specialHours()
            ->where('start_date', '<=', $now->toDateString())
            ->where('end_date', '>=', $now->toDateString())
            ->first();

        if ($specialHour) {
            if ($specialHour->is_closed) {
                return false;
            }
            if ($specialHour->open_time && $specialHour->close_time) {
                return $now->between($specialHour->open_time, $specialHour->close_time);
            }
        }

        // Check regular hours
        $hours = $this->operatingHours()->where('day_of_week', $dayOfWeek)->first();

        if (!$hours || $hours->is_closed) {
            return false;
        }

        if ($hours->open_time && $hours->close_time) {
            return $now->between($hours->open_time, $hours->close_time);
        }

        return false;
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
