<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use UsesUuid;

    protected $fillable = [
        'park_id',
        'user_id',
        'rating',
        'comment',
        'visit_date',
    ];

    protected $casts = [
        'rating' => 'integer',
        'visit_date' => 'date',
    ];

    public function park(): BelongsTo
    {
        return $this->belongsTo(Park::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
