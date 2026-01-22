<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatingHour extends Model
{
    use UsesUuid;

    protected $table = 'park_operating_hours';

    protected $fillable = [
        'park_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
    ];

    protected $casts = [
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
        'is_closed' => 'boolean',
    ];

    public function park(): BelongsTo
    {
        return $this->belongsTo(Park::class);
    }

    /**
     * Get formatted time range string
     */
    public function getFormattedHoursAttribute(): string
    {
        if ($this->is_closed) {
            return 'Fechado';
        }

        if (!$this->open_time || !$this->close_time) {
            return 'Horário não definido';
        }

        return $this->open_time->format('H:i') . ' às ' . $this->close_time->format('H:i');
    }

    /**
     * Get day name in Portuguese
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            'monday' => 'Segunda-feira',
            'tuesday' => 'Terça-feira',
            'wednesday' => 'Quarta-feira',
            'thursday' => 'Quinta-feira',
            'friday' => 'Sexta-feira',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo',
        ];

        return $days[$this->day_of_week] ?? $this->day_of_week;
    }
}
