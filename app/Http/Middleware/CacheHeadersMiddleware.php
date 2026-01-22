<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to add appropriate cache headers based on endpoint.
 * 
 * Cache-Control durations:
 * - Static data (states, tags): 24 hours
 * - Semi-static (featured cities, parks list): 5 minutes
 * - Dynamic (park details): 5 minutes
 * - User-specific (favorites, auth): No cache
 */
class CacheHeadersMiddleware
{
    /**
     * Cache durations in seconds for different route patterns.
     */
    private array $cacheDurations = [
        'states' => 86400,          // 24 hours
        'tags' => 86400,            // 24 hours
        'cities' => 3600,           // 1 hour
        'parks/home' => 300,        // 5 minutes
        'parks/search' => 60,       // 1 minute
        'parks' => 300,             // 5 minutes
    ];

    /**
     * Routes that should never be cached.
     */
    private array $noCacheRoutes = [
        'auth',
        'favorites',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only cache successful GET requests
        if ($request->method() !== 'GET' || !$response->isSuccessful()) {
            return $response;
        }

        $path = $request->path();

        // Check if route should not be cached
        foreach ($this->noCacheRoutes as $pattern) {
            if (str_contains($path, $pattern)) {
                return $response
                    ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', '0');
            }
        }

        // Find appropriate cache duration
        $maxAge = 0;
        foreach ($this->cacheDurations as $pattern => $duration) {
            if (str_contains($path, $pattern)) {
                $maxAge = $duration;
                break;
            }
        }

        if ($maxAge > 0) {
            // Generate ETag based on response content
            $etag = md5($response->getContent());

            // Check If-None-Match header
            $ifNoneMatch = $request->header('If-None-Match');
            if ($ifNoneMatch && $ifNoneMatch === "\"{$etag}\"") {
                return response('', 304);
            }

            $response
                ->header('Cache-Control', "public, max-age={$maxAge}")
                ->header('ETag', "\"{$etag}\"")
                ->header('Vary', 'Accept, Authorization');
        }

        return $response;
    }
}
