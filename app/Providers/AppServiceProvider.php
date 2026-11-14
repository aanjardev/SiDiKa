<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use App\Services\DinoyoWayContent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            Vite::createAssetPathsUsing(fn ($path, $secure = null) => asset('public/'.$path, $secure));
        }

        View::composer(['partials.header', 'partials.footer'], function ($view) {
            $view->with('wayNavigationLabel', app(DinoyoWayContent::class)->get()['navigation_label']);
        });

        if (env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
    }
}
