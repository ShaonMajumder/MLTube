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
        
        //for avatar support
        view()->composer('layouts.app', function ($view) {
            $theme = $this->getTheme();
            $view->with('theme', $theme);

            if (Auth::check()) {
                $channel = Channel::where('user_id', '=', auth()->user()->id )->first();
                $view->with('channel',$channel);
            }
        });

        User::observe(UserObserver::class);
        Channel::observe(ChannelObserver::class);
    }
    
    public function getTheme(){
        $sessionThemeKey = SessionEnum::THEME;
        $theme = session($sessionThemeKey);

        if (!$theme) {
            if (Auth::check()) {
                $userId = Auth::id();
                $cacheThemeKey = CacheEnum::THEME;
                $cacheThemeKey = "{$userId}_{$cacheThemeKey}";
                $theme = Redis::get($cacheThemeKey);
                
                if (!$theme) {
                    $theme = Settings::where('user_id', $userId)
                                        ->where('key', CacheEnum::THEME)
                                        ->value('value');
                        
                    if (!$theme) {
                        $theme = config('theme.default');
                        
                        $result = Settings::updateOrInsert(
                            [
                                'user_id' => $userId,
                                'key' => 'theme'
                            ], 
                            [
                                'value' => $theme,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        );
                    }
                    Redis::set($cacheThemeKey, $theme);
                }
            } else {
                $theme = config('theme.default');
            }

            session([$sessionThemeKey => $theme]);
        }

        return $theme;
    }
}
