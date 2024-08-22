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
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('theme', function ($app) {
            return $this->getTheme();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        
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