<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Log\LogManager;
use App\Logging\MonthlyLogger;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make(LogManager::class)->extend('monthly', function ($app, $config) {
            return (new MonthlyLogger)($config);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
