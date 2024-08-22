<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewParamsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        view()->composer('layouts.app', function ($view) {
            $channel = auth()->check() ? auth()->user()->channel : null; //for avatar support
            $theme = app('theme');
            $view->with('channel', $channel)
                 ->with('theme', $theme);
            // View::share('theme', $theme);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        
        
    }
}