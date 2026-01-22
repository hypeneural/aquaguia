<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherCache extends Model
{
    protected $table = 'weather_cache';

    protected $fillable = [
        'provider',
        'kind',
        'lat',
        'lon',
        'units',
        'lang',
        'fetched_at',
        'next_refresh_at',
        'ttl_hours',
        'payload',
        'status',
        'error_message',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lon' => 'decimal:7',
        'fetched_at' => 'datetime',
        'next_refresh_at' => 'datetime',
        'ttl_hours' => 'integer',
        'payload' => 'array',
    ];

    /**
     * Check if cache is still valid
     */
    public function isValid(): bool
    {
        return $this->next_refresh_at && $this->next_refresh_at->isFuture();
    }

    /**
     * Check if cache is stale (expired)
     */
    public function isStale(): bool
    {
        return !$this->isValid();
    }

    /**
     * Get cache status for API response
     */
    public function getCacheStatus(bool $wasRefreshed = false): string
    {
        if ($wasRefreshed) {
            return 'refreshed';
        }

        if ($this->status === 'error') {
            return 'stale';
        }

        return $this->isValid() ? 'hit' : 'miss';
    }

    /**
     * Get daily summary from forecast data
     */
    public function getDailySummary(): array
    {
        $payload = $this->payload;
        if (!isset($payload['list']) || !is_array($payload['list'])) {
            return [];
        }

        $days = [];

        foreach ($payload['list'] as $item) {
            $date = date('Y-m-d', $item['dt']);

            if (!isset($days[$date])) {
                $days[$date] = [
                    'date' => $date,
                    'temp_min' => $item['main']['temp_min'],
                    'temp_max' => $item['main']['temp_max'],
                    'pop_max' => $item['pop'] ?? 0,
                    'rain_total_mm' => 0,
                    'icon' => $item['weather'][0]['icon'] ?? null,
                ];
            }

            $days[$date]['temp_min'] = min($days[$date]['temp_min'], $item['main']['temp_min']);
            $days[$date]['temp_max'] = max($days[$date]['temp_max'], $item['main']['temp_max']);
            $days[$date]['pop_max'] = max($days[$date]['pop_max'], $item['pop'] ?? 0);

            if (isset($item['rain']['3h'])) {
                $days[$date]['rain_total_mm'] += $item['rain']['3h'];
            }
        }

        return array_values($days);
    }

    /**
     * Transform forecast items to clean format
     */
    public function getForecastItems(): array
    {
        $payload = $this->payload;
        if (!isset($payload['list']) || !is_array($payload['list'])) {
            return [];
        }

        return array_map(function ($item) {
            return [
                'at' => date('c', $item['dt']),
                'temp' => [
                    'value' => $item['main']['temp'],
                    'feels_like' => $item['main']['feels_like'],
                    'min' => $item['main']['temp_min'],
                    'max' => $item['main']['temp_max'],
                ],
                'pressure_hpa' => $item['main']['pressure'] ?? null,
                'humidity' => $item['main']['humidity'],
                'clouds' => $item['clouds']['all'] ?? 0,
                'wind' => [
                    'speed' => $item['wind']['speed'],
                    'deg' => $item['wind']['deg'],
                    'gust' => $item['wind']['gust'] ?? null,
                ],
                'visibility_m' => $item['visibility'] ?? null,
                'pop' => $item['pop'] ?? 0,
                'rain_3h_mm' => $item['rain']['3h'] ?? null,
                'snow_3h_mm' => $item['snow']['3h'] ?? null,
                'weather' => [
                    'id' => $item['weather'][0]['id'],
                    'main' => $item['weather'][0]['main'],
                    'description' => $item['weather'][0]['description'],
                    'icon' => $item['weather'][0]['icon'],
                ],
                'day_night' => $item['sys']['pod'] ?? null,
            ];
        }, $payload['list']);
    }
}
