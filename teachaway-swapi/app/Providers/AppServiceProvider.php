<?php

namespace App\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(Client::class, function () {
            return new Client([
                'base_uri' => env('SWAPI_URL'),
                RequestOptions::CONNECT_TIMEOUT => env('GUZZLE_TIMEOUT', 10),
                RequestOptions::TIMEOUT => env('GUZZLE_TIMEOUT', 60),
            ]);
        });
    }
}
