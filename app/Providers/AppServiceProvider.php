<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('contact', function (Request $request) {
            return [Limit::perMinutes(10, 3)->by($request->ip()), Limit::perDay(20)->by($request->ip())];
        });

        RateLimiter::for('redirect', function (Request $request) {
            return Limit::perMinute(config('shortlink.redirect_rate_limit', 120))->by($request->ip());
        });
    }
}
