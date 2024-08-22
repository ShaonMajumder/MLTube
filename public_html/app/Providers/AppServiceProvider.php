<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\Paginator;

use App\Enums\SessionEnum;
use App\Enums\CacheEnum;
use App\Models\Channel;
use App\Models\Browsing;
use App\Models\Settings;
use App\Models\User;
use App\Observers\ChannelObserver;
use App\Observers\UserObserver;

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
        User::observe(UserObserver::class);
        Channel::observe(ChannelObserver::class);
    }
}
