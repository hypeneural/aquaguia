<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComfortPoint extends Model
{
    use UsesUuid;

    protected $fillable = [
        'park_id',
        'type',
        'label',
        'x',
        'y',
    ];

    protected $casts = [
        'x' => 'decimal:2',
        'y' => 'decimal:2',
    ];

    public function park(): BelongsTo
    {
        return $this->belongsTo(Park::class);
    }
}
