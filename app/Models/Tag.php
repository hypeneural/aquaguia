<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Tag extends Model
{
    use UsesUuid, HasSlug;

    protected $fillable = ['slug', 'label', 'emoji', 'color'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('label')
            ->saveSlugsTo('slug');
    }

    public function parks(): BelongsToMany
    {
        return $this->belongsToMany(Park::class, 'park_tags');
    }
}
