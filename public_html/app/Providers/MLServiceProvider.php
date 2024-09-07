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

class MLServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SessionEnum::ML_TAGS_CONFIDENCE, function ($app) {
            return $this->getMLTagsConfidence();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        
    }

    public function getMLTagsConfidence(){
        $sessionMLTagsConfidenceKey = SessionEnum::ML_TAGS_CONFIDENCE;
        $mlConfidence = session($sessionMLTagsConfidenceKey);

        if (!$mlConfidence) {
            // if (Auth::check()) {
            //     $userId = Auth::id();
                $cacheMLTagsConfidenceKey = CacheEnum::ML_TAGS_CONFIDENCE;
                $mlConfidence = Redis::get($cacheMLTagsConfidenceKey);
                
                if (!$mlConfidence) {
                    $mlConfidence = Settings::where('key', CacheEnum::ML_TAGS_CONFIDENCE)
                                            ->value('value');
                        
                    if (!$mlConfidence) {
                        $mlConfidence = config('ml.tags.confidence.default');
                        
                        $result = Settings::updateOrInsert(
                            [
                                'key' => CacheEnum::ML_TAGS_CONFIDENCE
                            ], 
                            [
                                'value' => $mlConfidence,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]
                        );
                    }
                    Redis::set($cacheMLTagsConfidenceKey, $mlConfidence);
                }
            // } else {
            //     $mlConfidence = config('ml.tags.confidence.default');
            // }

            session([$sessionMLTagsConfidenceKey => $mlConfidence]);
        }

        return $mlConfidence;
    }
}