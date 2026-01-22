<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Park;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(
        private WeatherService $weatherService
    ) {
    }

    /**
     * GET /parks/{park}/weather
     * 
     * Get 5-day/3-hour weather forecast for a park.
     * Uses on-demand caching (6h TTL by default).
     * 
     * Query params:
     *   - units: metric|standard|imperial (default: metric)
     *   - lang: language code (default: pt_br)
     *   - include: "raw" to include raw OpenWeather payload
     *   - force: boolean to force cache refresh (admin only)
     */
    public function show(Park $park, Request $request): JsonResponse
    {
        // Validate park has coordinates
        if (!$park->latitude || !$park->longitude) {
            return response()->json([
                'ok' => false,
                'error' => [
                    'code' => 'NO_COORDINATES',
                    'message' => 'Este parque não possui coordenadas geográficas cadastradas.',
                ],
            ], 422);
        }

        $units = $request->get('units', 'metric');
        $lang = $request->get('lang', 'pt_br');
        $includeRaw = $request->boolean('include') && $request->get('include') === 'raw';
        $forceRefresh = $request->boolean('force');

        try {
            $result = $this->weatherService->forecast5(
                (float) $park->latitude,
                (float) $park->longitude,
                $units,
                $lang,
                6,
                $forceRefresh
            );

            $cache = $result['cache'];
            $refreshed = $result['refreshed'] ?? false;
            $error = $result['error'] ?? null;

            // Build response following the schema
            $response = [
                'ok' => $cache->status === 'ok',

                'provider' => [
                    'name' => $cache->provider,
                    'product' => $cache->kind,
                    'endpoint' => '/data/2.5/forecast',
                    'units' => $cache->units,
                    'lang' => $cache->lang,
                ],

                'cache' => [
                    'status' => $cache->getCacheStatus($refreshed),
                    'ttl_hours' => $cache->ttl_hours,
                    'fetched_at' => $cache->fetched_at?->toIso8601String(),
                    'next_refresh_at' => $cache->next_refresh_at?->toIso8601String(),
                    'refreshed' => $refreshed,
                    'stale_reason' => $error,
                ],

                'location' => [
                    'park_id' => $park->id,
                    'park_name' => $park->name,
                    'lat' => (float) $park->latitude,
                    'lon' => (float) $park->longitude,
                    'city' => $park->city?->name,
                    'state' => $park->city?->state?->abbr,
                    'country' => 'BR',
                    'timezone_offset_seconds' => $cache->payload['city']['timezone'] ?? -10800,
                ],

                'forecast' => [
                    'interval_seconds' => 10800, // 3 hours
                    'items' => $cache->getForecastItems(),
                    'daily_summary' => $cache->getDailySummary(),
                ],
            ];

            // Include raw payload if requested
            if ($includeRaw) {
                $response['raw'] = $cache->payload;
            }

            // Add error info if status is not ok
            if ($cache->status !== 'ok') {
                $response['error'] = [
                    'code' => 'STALE_CACHE',
                    'message' => $cache->error_message ?? 'Cache is stale, using previous data.',
                ];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => [
                    'code' => 'WEATHER_UNAVAILABLE',
                    'message' => 'Não foi possível obter dados meteorológicos para este parque.',
                    'detail' => config('app.debug') ? $e->getMessage() : null,
                ],
            ], 503);
        }
    }

    /**
     * GET /parks/{park}/weather/current
     * 
     * Get current weather for a park (simpler, shorter cache).
     */
    public function current(Park $park, Request $request): JsonResponse
    {
        if (!$park->latitude || !$park->longitude) {
            return response()->json([
                'ok' => false,
                'error' => [
                    'code' => 'NO_COORDINATES',
                    'message' => 'Este parque não possui coordenadas geográficas cadastradas.',
                ],
            ], 422);
        }

        $units = $request->get('units', 'metric');
        $lang = $request->get('lang', 'pt_br');

        try {
            $result = $this->weatherService->current(
                (float) $park->latitude,
                (float) $park->longitude,
                $units,
                $lang
            );

            $cache = $result['cache'];
            $payload = $cache->payload;

            return response()->json([
                'ok' => true,
                'cache' => [
                    'status' => $cache->getCacheStatus($result['refreshed'] ?? false),
                    'fetched_at' => $cache->fetched_at?->toIso8601String(),
                    'next_refresh_at' => $cache->next_refresh_at?->toIso8601String(),
                ],
                'location' => [
                    'park_id' => $park->id,
                    'park_name' => $park->name,
                    'lat' => (float) $park->latitude,
                    'lon' => (float) $park->longitude,
                ],
                'current' => [
                    'temp' => $payload['main']['temp'] ?? null,
                    'feels_like' => $payload['main']['feels_like'] ?? null,
                    'humidity' => $payload['main']['humidity'] ?? null,
                    'clouds' => $payload['clouds']['all'] ?? null,
                    'wind_speed' => $payload['wind']['speed'] ?? null,
                    'weather' => [
                        'main' => $payload['weather'][0]['main'] ?? null,
                        'description' => $payload['weather'][0]['description'] ?? null,
                        'icon' => $payload['weather'][0]['icon'] ?? null,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => [
                    'code' => 'WEATHER_UNAVAILABLE',
                    'message' => 'Não foi possível obter o clima atual.',
                ],
            ], 503);
        }
    }
}
