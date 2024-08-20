<?php


namespace App\Providers;

// use App\Enums\\Permissions;
use App\Enums\Roles;
use Laratrust\LaratrustServiceProvider as OriginalLaratrustServiceProvider;
use Illuminate\Support\Facades\Route;

class LaratrustServiceProvider extends OriginalLaratrustServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->singleton('dashboard_suffix', function ($app) {
        //     return 'admin';
        // });

        // $this->app->singleton('reserved_role', function ($app) {
        //     return Roles::ADMIN;
        // });
        
        // $this->app->singleton('reserved_permissions_for_reserved_role', function ($app) {
        //     return [
        //         Permissions::USER_CAN_VIEW_LIST,
        //         Permissions::USER_CAN_CREATE,
        //         Permissions::USER_CAN_EDIT,
        //         Permissions::USER_UPDATE_SETTINGS,
    
        //         Permissions::CAN_MANAGE_ROLE,
        //         Permissions::CAN_PERMISSION_MANAGE,
        //         Permissions::CAN_ROLE_PERMISSION_ASSIGNMENT_MANAGE
        //     ];
        // });

        $this->registerRoutes();
        $this->registerResources();
        $this->configure();
        $this->offerPublishing();
        $this->registerLaratrust();
        $this->registerCommands();
    }

    /**
     * Register all the possible views used by Laratrust.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(resource_path('views/laratrust'), 'laratrust');
    }

    protected function registerRoutes()
    {
        if (!$this->app['config']->get('laratrust.panel.register')) {
            return;
        }

        Route::group([
            'prefix' => config('laratrust.panel.path'),
            'namespace' => 'App\Http\Controllers\Laratrust',
            'middleware' => config('laratrust.panel.middleware', 'web'),
        ], function () {
            Route::redirect('/', '/' . config('laratrust.panel.path') . '/roles-assignment');
            $this->loadRoutesFrom(base_path('routes/laratrust.php'));
        });
    }

}
