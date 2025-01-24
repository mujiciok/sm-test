<?php

namespace App\Providers;

use App\Services\DataForSeoClient\RestClient;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RestClient::class, function (Application $app) {
            return new RestClient(
                config('data_for_seo.api_host'),
                null,
                config('data_for_seo.login'),
                config('data_for_seo.password'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
