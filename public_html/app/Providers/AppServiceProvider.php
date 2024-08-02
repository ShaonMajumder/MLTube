<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use App\Models\Channel;

use Illuminate\Support\Facades\Auth;

use App\Models\Browsing;
use App\Models\User;
use App\Observers\ChannelObserver;
use App\Observers\UserObserver;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        Paginator::useBootstrap();
        
        //for avatar support
        view()->composer('layouts.app', function ($view) {
            if (Auth::check()) {
                $channel = Channel::where('user_id', '=', auth()->user()->id )->first();
                $view->with('channel',$channel);
            }
        });

        User::observe(UserObserver::class);
        Channel::observe(ChannelObserver::class);
    }
    
}
