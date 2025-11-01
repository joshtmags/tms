<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting()
    {
        /**
         * expand for rate limit configuration base on client's need
         */

        RateLimiter::for("api", function (Request $request) {
            // Base limit for authenticated users
            if ($request->user()) {
                return Limit::perMinute(120)
                    ->by($request->user()->id)->response(function (Request $request, array $headers) {
                        return response()->json([
                            "success" => false,
                            "message" => "Too many requests. Please try again later.",
                            "retry_after" => $headers["Retry-After"] ?? 60,
                        ], 429);
                    });
            }

            // Lower limit for unauthenticated users
            return Limit::perMinute(30)->by($request->ip())->response(function (Request $request, array $headers) {
                return response()->json([
                    "success" => false,
                    "message" => "Too many requests. Please authenticate or try again later.",
                    "retry_after" => $headers["Retry-After"] ?? 60,
                ], 429);
            });
        });
    }
}
