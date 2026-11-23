<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        Model::shouldBeStrict(! $this->app->isProduction());

        // Datas por extenso em português ("18 de novembro de 2026")
        Carbon::setLocale(config('app.locale'));

        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));

        RateLimiter::for('contact', fn (Request $request) => Limit::perMinute(3)->by($request->ip()));

        RateLimiter::for('banking', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
    }
}
