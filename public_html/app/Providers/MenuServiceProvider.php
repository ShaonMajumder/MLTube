<?php

namespace App\Providers;

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
        menu()->addMenu('dashboard', 'Dashboard', 'fas fa-tachometer-alt');
        menu()->addMenu('profile', 'Profile', 'fas fa-user');
        menu()->addMenu('role_permissions', 'Role Permissions', 'fas fa-cog', [], null);
        menu()->addChilds('role_permissions', [
            [ 'key' => 'permissions', 'label' => 'Permissions', 'route' => '/settings/general', 'icon' => 'fas fa-cogs' ],
            [ 'key' => 'roles', 'label' => 'Roles', 'route' => '/settings/security', 'icon' => 'fas fa-shield-alt' ],
            [ 'key' => 'role_assignment', 'label' => 'Role Assignment', 'route' => '/settings/notifications', 'icon' => 'fas fa-bell' ]
        ]);
    }
}
