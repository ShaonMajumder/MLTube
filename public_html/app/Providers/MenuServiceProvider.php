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

        menu()->addMenu('channel', [
            'label' => 'Channel',
            'permissions' => PermissionEnum::CHANNEL_OWNED,
            'icon' => 'fas fa-tv',
        ]);

        menu()->addChilds('channel', [
            [
                'key' => CommonEnum::CHANNEL_OWNED, 
                'label' => 'My Channel', 
                'route' => RouteEnum::CHANNELS_SHOW,
                'route_parameters' => $channel ?  ['channel' => $channel->id] : ['channel' => 'invalid'] , // $channel ?  ['channel' => $channel->id] : [],
                'permissions' => PermissionEnum::CHANNEL_OWNED,
                'icon' => 'fas fa-user-circle'
            ],
            [
                'key' => CommonEnum::CHANNEL_SUBSCRIBERS, 
                'label' => 'Subscribers', 
                'route' => RouteEnum::CHANNEL_SUBSCRIBERS,
                'route_parameters' => $channel ?  ['channel' => $channel->id] : ['channel' => 'invalid'] , // $channel ?  ['channel' => $channel->id] : [],
                'permissions' => PermissionEnum::CHANNEL_SUBSCRIBERS,
                'icon' => 'fas fa-user',
            ],
            [
                'key' => CommonEnum::USER_CHANNEL_SUBSCRIPTIONS,
                'label' => 'Subscriptions', 
                'route' => RouteEnum::USER_CHANNEL_SUBSCRIPTIONS,
                'route_parameters' => auth()->user() ?  ['user' => auth()->user()->id] : ['user' => 'invalid'] , // $channel ?  ['channel' => $channel->id] : [],
                'permissions' => PermissionEnum::USER_CHANNEL_SUBSCRIPTIONS,
                'icon' => 'fas fa-bell',
            ]
        ]);


        menu()->addMenu('manage-site', [
            'label' => 'Manage Site',
            'permissions' => PermissionEnum::ADMIN_MANAGE_SITE,
            'icon' => 'fas fa-cog',
        ]);

        menu()->addChilds('manage-site', [
            [
                'key' => 'data', 
                'label' => 'Data',
                'route' => RouteEnum::ADMIN_MANAGE_SITE,
                'permissions' => PermissionEnum::CHANNEL_OWNED,
                'icon' => 'fas fa-user-circle'
            ],
            [
                'key' => 'push-notification', 
                'label' => 'Push Notification', 
                'route' => RouteEnum::ADMIN_MANAGE_SITE_PUSH_NOTIFICATION,
                // 'route_parameters' => $channel ?  ['channel' => $channel->id] : ['channel' => 'invalid'] , // $channel ?  ['channel' => $channel->id] : [],
                // 'permissions' => PermissionEnum::CHANNEL_OWNED,
                'icon' => 'fas fa-user-circle'
            ],
            [
                'key' => 'sitemap', 
                'label' => 'Sitemap', 
                // 'route' => RouteEnum::CHANNELS_SHOW,
                'route_parameters' => $channel ?  ['channel' => $channel->id] : ['channel' => 'invalid'] , // $channel ?  ['channel' => $channel->id] : [],
                'permissions' => PermissionEnum::CHANNEL_OWNED,
                'icon' => 'fas fa-user-circle'
            ],
            [
                'key' => 'routes', 
                'label' => 'Routes', 
                // 'route' => RouteEnum::CHANNELS_SHOW,
                'route_parameters' => $channel ?  ['channel' => $channel->id] : ['channel' => 'invalid'] , // $channel ?  ['channel' => $channel->id] : [],
                'permissions' => PermissionEnum::CHANNEL_OWNED,
                'icon' => 'fas fa-user-circle'
            ],
        ]);
        
        
        
        menu()->addMenu('role_permissions', [
            'label' => 'Role Permissions',
            'permissions' => PermissionEnum::ROLE_PERMISSION,
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