<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkVideo extends Model
{
    use UsesUuid;

    protected $fillable = [
        'park_id',
        'youtube_id',
        'title',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    public function park(): BelongsTo
    {
        return $this->belongsTo(Park::class);
    }
}
