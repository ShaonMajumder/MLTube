<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class CustomMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Log::info('mapResourcePermissions macro registered.');
        PendingResourceRegistration::macro('mapResourcePermissions', function (array $permissions) {
            Log::info('mapResourcePermissions macro registered. 2');
            // Register the resource routes
            $this->register();

            // Apply middleware to the registered routes
            foreach ($permissions as $action => $permission) {
                $resourceName = $this->name . '.' . $action;

                foreach (Route::getRoutes() as $route) {
                    $resourceName = Str::of($resourceName)->replace(['/', '{', '}'], ['.', '', '']);
                    // if($resourceName == $route->getName()){
                    //     echo gettype($resourceName) . ' == ' . gettype($route->getName()) . '<br>';
                    //     echo $resourceName . ' == ' . $route->getName() . '<br>';
                    // }
                    if ($route instanceof IlluminateRoute && $resourceName == $route->getName()) {
                        $existingMiddleware = $route->middleware();
                        $route->middleware(array_merge($existingMiddleware, [$permission]));
                    }
                }
            }

            return $this;
        });
    }
}
