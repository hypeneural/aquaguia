<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class City extends Model
{
    use UsesUuid, HasSlug;

    protected $fillable = ['state_id', 'name', 'slug', 'image', 'featured'];

    protected $casts = [
        'featured' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function parks(): HasMany
    {
        return $this->hasMany(Park::class);
    }
}
