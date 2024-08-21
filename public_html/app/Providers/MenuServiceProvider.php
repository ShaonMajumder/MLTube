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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){
        menu()->addMenu('dashboard', '', null, 'Dashboard', 'fas fa-tachometer-alt');
        menu()->addMenu('profile', '', PermissionEnum::ROLE_PERMISSION, 'Profile', 'fas fa-user');
        menu()->addMenu('role_permissions', '', PermissionEnum::ROLE_PERMISSION, 'Role Permissions', 'fas fa-cog', [], null);
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