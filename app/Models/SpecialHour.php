<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialHour extends Model
{
    use UsesUuid;

    protected $table = 'park_special_hours';

    protected $fillable = [
        'park_id',
        'name',
        'start_date',
        'end_date',
        'open_time',
        'close_time',
        'is_closed',
        'closure_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
        'is_closed' => 'boolean',
    ];

    public function park(): BelongsTo
    {
        return $this->belongsTo(Park::class);
    }

    /**
     * Check if this special hour applies to a given date
     */
    public function appliesToDate(\DateTimeInterface $date): bool
    {
        return $date >= $this->start_date && $date <= $this->end_date;
    }

    /**
     * Get formatted date range
     */
    public function getFormattedPeriodAttribute(): string
    {
        return $this->start_date->format('d/m/Y') . ' a ' . $this->end_date->format('d/m/Y');
    }
}
