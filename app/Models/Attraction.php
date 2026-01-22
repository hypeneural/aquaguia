<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attraction extends Model
{
    use UsesUuid;

    protected $fillable = [
        'park_id',
        'name',
        'type',
        'min_height_cm',
        'max_height_cm',
        'adrenaline',
        'avg_queue_minutes',
        'description',
        'has_double_float',
        'image',
        'is_open',
        'display_order',
    ];

    protected $casts = [
        'min_height_cm' => 'integer',
        'max_height_cm' => 'integer',
        'adrenaline' => 'integer',
        'avg_queue_minutes' => 'integer',
        'has_double_float' => 'boolean',
        'is_open' => 'boolean',
        'display_order' => 'integer',
    ];

    public function park(): BelongsTo
    {
        return $this->belongsTo(Park::class);
    }
}
