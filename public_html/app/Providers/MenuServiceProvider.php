<?php

namespace App\Providers;

use App\Enums\CommonEnum;
use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Enums\RouteEnum;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        view()->composer('layouts.app', function ($view) {
            $this->setMenu();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){

    }

    public function setMenu(){
        $channel = auth()->check() ? auth()->user()->channel : null;
        
        menu()->addMenu('dashboard', [
            'label' => 'Dashboard',
            'route' => RouteEnum::HOME,
            'icon' => 'fas fa-tachometer-alt',
        ]);

        menu()->addMenu('subscribers', [
            'label' => 'Subscribers',
            'route' => RouteEnum::CHANNEL_SUBSCRIPTIONS,
            'route_parameters' => $channel ?  ['channel' => $channel->id] : ['channel' => 'invalid'] , // $channel ?  ['channel' => $channel->id] : [],
            'permissions' => PermissionEnum::CHANNEL_SUBSCRIPTIONS,
            'icon' => 'fas fa-tachometer-alt',
        ]);

        menu()->addMenu('subscription', [
            'label' => 'Subscription',
            'route' => RouteEnum::HOME,
            'icon' => 'fas fa-tachometer-alt',
        ]);
        
        menu()->addMenu('my_channel', [
            'label' => 'My Channel',
            'route' => RouteEnum::CHANNELS_SHOW,
            'route_parameters' => $channel ?  ['channel' => $channel->id] : ['channel' => 'invalid'] , // $channel ?  ['channel' => $channel->id] : [],
            'middleware' => ['auth'],
            'icon' => 'fas fa-cog'
        ]);
        
        menu()->addMenu('role_permissions', [
            'route' => '',
            'permissions' => PermissionEnum::ROLE_PERMISSION,
            'label' => 'Role Permissions',
            'icon' => 'fas fa-cog'
        ]);
        
        menu()->addChilds('role_permissions', [
            [
                'key' => CommonEnum::MANAGE_PERMISSION, 
                'label' => 'Permissions', 
                'route' => RouteEnum::MANAGE_PERMISSION,
                'permissions' => PermissionEnum::MANAGE_PERMISSION,
                'icon' => 'fas fa-cogs'
            ],
            [
                'key' => CommonEnum::MANAGE_ROLE, 
                'label' => 'Roles', 
                'route' => RouteEnum::MANAGE_ROLE, 
                'permissions' => PermissionEnum::MANAGE_ROLE,
                'icon' => 'fas fa-shield-alt'
            ],
            [
                'key' => CommonEnum::MANAGE_ROLE_ASSIGNMENT,
                'label' => 'Role Assignment', 
                'route' => RouteEnum::MANAGE_ROLE_ASSIGNMENT,
                'permissions' => PermissionEnum::MANAGE_ROLE_ASSIGNMENT,
                'icon' => 'fas fa-bell'
            ]
        ]);
    }
}