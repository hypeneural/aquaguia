<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCacheHeaders
{
    /**
     * Add ETag and cache headers to API responses.
     * Supports conditional requests with If-None-Match.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply to GET requests and JSON responses
        if (!$request->isMethod('GET') || !$response instanceof JsonResponse) {
            return $response;
        }

        // Skip for authenticated/personalized endpoints
        $skipPaths = ['/auth/', '/favorites', '/me'];
        foreach ($skipPaths as $path) {
            if (str_contains($request->path(), $path)) {
                return $response;
            }
        }

        // Generate ETag from response content
        $content = $response->getContent();
        $etag = '"' . md5($content) . '"';

        // Check If-None-Match header
        $ifNoneMatch = $request->header('If-None-Match');
        if ($ifNoneMatch === $etag) {
            return response()->json(null, 304)->header('ETag', $etag);
        }

        // Determine cache duration based on endpoint
        $cacheDuration = $this->getCacheDuration($request->path());

        // Add headers
        $response->headers->set('ETag', $etag);
        $response->headers->set('Cache-Control', "public, max-age={$cacheDuration}");
        $response->headers->set('Last-Modified', now()->toRfc7231String());

        return $response;
    }

    /**
     * Get cache duration in seconds based on endpoint.
     */
    private function getCacheDuration(string $path): int
    {
        // Static data - cache for 24 hours
        if (str_contains($path, '/states') || str_contains($path, '/tags') || str_contains($path, '/cities')) {
            return 86400; // 24 hours
        }

        // Home data - cache for 5 minutes
        if (str_contains($path, '/home')) {
            return 300; // 5 minutes
        }

        // Park list - cache for 2 minutes
        if (preg_match('/\/parks$/', $path)) {
            return 120; // 2 minutes
        }

        // Park detail - cache for 5 minutes
        if (preg_match('/\/parks\/[^\/]+$/', $path)) {
            return 300; // 5 minutes
        }

        // Default - cache for 1 minute
        return 60;
    }
}
