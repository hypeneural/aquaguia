<?php

namespace App\Services;

use App\Models\WeatherCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Get 5-day / 3-hour forecast with on-demand caching.
     * Only calls OpenWeather API when cache is expired (6h default).
     * Uses lock to prevent thundering herd problem.
     */
    public function forecast5(
        float $lat,
        float $lon,
        string $units = 'metric',
        string $lang = 'pt_br',
        int $ttlHours = 6,
        bool $forceRefresh = false
    ): array {
        // Round coordinates to 4 decimal places for better cache hits
        $lat = round($lat, 4);
        $lon = round($lon, 4);

        $row = WeatherCache::query()
            ->where([
                'provider' => 'openweather',
                'kind' => 'forecast5',
                'lat' => $lat,
                'lon' => $lon,
                'units' => $units,
                'lang' => $lang,
            ])->first();

        // Cache hit - return if still valid and not forcing refresh
        if ($row && $row->isValid() && !$forceRefresh) {
            return [
                'cache' => $row,
                'refreshed' => false,
            ];
        }

        // Use lock to prevent multiple simultaneous API calls
        $lockKey = "weather:lock:forecast5:{$lat}:{$lon}:{$units}:{$lang}";

        return Cache::lock($lockKey, 15)->block(5, function () use ($lat, $lon, $units, $lang, $ttlHours, $row) {
            // Re-check after acquiring lock (another request may have refreshed)
            $row = WeatherCache::query()
                ->where([
                    'provider' => 'openweather',
                    'kind' => 'forecast5',
                    'lat' => $lat,
                    'lon' => $lon,
                    'units' => $units,
                    'lang' => $lang,
                ])->first();

            if ($row && $row->isValid()) {
                return [
                    'cache' => $row,
                    'refreshed' => false,
                ];
            }

            // Fetch from OpenWeather API
            try {
                $response = Http::timeout(10)
                    ->retry(2, 250)
                    ->get('https://api.openweathermap.org/data/2.5/forecast', [
                        'lat' => $lat,
                        'lon' => $lon,
                        'appid' => config('services.openweather.key'),
                        'units' => $units,
                        'lang' => $lang,
                    ]);

                if (!$response->successful()) {
                    return $this->handleApiError($row, $response->status(), $response->body());
                }

                $data = $response->json();

                // Validate response has expected structure
                if (!isset($data['list']) || !is_array($data['list'])) {
                    return $this->handleApiError($row, 0, 'Invalid response structure');
                }

                // Create or update cache
                $row = $row ?? new WeatherCache();
                $row->fill([
                    'provider' => 'openweather',
                    'kind' => 'forecast5',
                    'lat' => $lat,
                    'lon' => $lon,
                    'units' => $units,
                    'lang' => $lang,
                    'payload' => $data,
                    'status' => 'ok',
                    'error_message' => null,
                    'fetched_at' => now(),
                    'ttl_hours' => $ttlHours,
                    'next_refresh_at' => now()->addHours($ttlHours),
                ]);
                $row->save();

                Log::info('Weather cache refreshed', [
                    'lat' => $lat,
                    'lon' => $lon,
                    'items' => count($data['list']),
                ]);

                return [
                    'cache' => $row,
                    'refreshed' => true,
                ];

            } catch (\Exception $e) {
                Log::error('Weather API exception', [
                    'lat' => $lat,
                    'lon' => $lon,
                    'error' => $e->getMessage(),
                ]);

                return $this->handleApiError($row, 0, $e->getMessage());
            }
        });
    }

    /**
     * Handle API errors gracefully - return stale cache if available
     */
    private function handleApiError(?WeatherCache $row, int $statusCode, string $message): array
    {
        $errorMessage = "HTTP {$statusCode}: {$message}";

        if ($row) {
            // Return stale cache with error status
            $row->status = 'error';
            $row->error_message = $errorMessage;
            $row->next_refresh_at = now()->addMinutes(30); // Retry in 30min
            $row->save();

            return [
                'cache' => $row,
                'refreshed' => false,
                'error' => $errorMessage,
            ];
        }

        // No cache available, throw exception
        throw new \RuntimeException("OpenWeather API error: {$errorMessage}");
    }

    /**
     * Get current weather (optional, simpler endpoint)
     */
    public function current(float $lat, float $lon, string $units = 'metric', string $lang = 'pt_br'): array
    {
        $lat = round($lat, 4);
        $lon = round($lon, 4);

        $row = WeatherCache::query()
            ->where([
                'provider' => 'openweather',
                'kind' => 'current',
                'lat' => $lat,
                'lon' => $lon,
                'units' => $units,
                'lang' => $lang,
            ])->first();

        // Current weather has shorter TTL (1 hour)
        if ($row && $row->isValid()) {
            return [
                'cache' => $row,
                'refreshed' => false,
            ];
        }

        $response = Http::timeout(10)->get('https://api.openweathermap.org/data/2.5/weather', [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => config('services.openweather.key'),
            'units' => $units,
            'lang' => $lang,
        ]);

        if (!$response->successful()) {
            if ($row) {
                return ['cache' => $row, 'refreshed' => false, 'error' => 'API error'];
            }
            throw new \RuntimeException("Weather API error: {$response->status()}");
        }

        $row = $row ?? new WeatherCache();
        $row->fill([
            'provider' => 'openweather',
            'kind' => 'current',
            'lat' => $lat,
            'lon' => $lon,
            'units' => $units,
            'lang' => $lang,
            'payload' => $response->json(),
            'status' => 'ok',
            'error_message' => null,
            'fetched_at' => now(),
            'ttl_hours' => 1,
            'next_refresh_at' => now()->addHour(),
        ]);
        $row->save();

        return [
            'cache' => $row,
            'refreshed' => true,
        ];
    }
}
