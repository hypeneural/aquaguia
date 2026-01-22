<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkFaq extends Model
{
    use UsesUuid;

    protected $table = 'park_faq';

    protected $fillable = [
        'park_id',
        'question',
        'answer',
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
