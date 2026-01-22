<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

/**
 * Rate Limiting Configuration
 * 
 * Limits:
 * - Guest: 60 requests/minute
 * - Authenticated: 300 requests/minute  
 * - Search: 30 requests/minute (to prevent abuse)
 */
class RateLimitServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Standard API rate limit
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user('api');

            if ($user) {
                // Authenticated users: 300 requests/minute
                return Limit::perMinute(300)->by($user->id);
            }

            // Guests: 60 requests/minute by IP
            return Limit::perMinute(60)->by($request->ip());
        });

        // Search-specific rate limit (stricter)
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by(
                $request->user('api')?->id ?: $request->ip()
            );
        });

        // Auth endpoints rate limit (prevent brute force)
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
