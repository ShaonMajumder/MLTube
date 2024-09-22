<?php

namespace App\Providers;

use App\Enums\CacheEnum;
use App\Enums\CommonEnum;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Enums\RouteEnum;
use App\Enums\SessionEnum;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(PushNotificationRepositoryInterface::class, PushNotificationRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        
    }
}