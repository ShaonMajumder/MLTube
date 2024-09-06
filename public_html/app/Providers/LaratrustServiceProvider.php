<?php


namespace App\Providers;

// use App\Enums\\Permissions;
use App\Enums\Roles;
use Illuminate\Foundation\AliasLoader;
use Laratrust\LaratrustServiceProvider as OriginalLaratrustServiceProvider;
use Illuminate\Support\Facades\Route;
use ReflectionClass;

class LaratrustServiceProvider extends OriginalLaratrustServiceProvider
{
    // /**
    //  * Register the service provider.
    //  *
    //  * @return void
    //  */
    // public function register()
    // {
    //     $this->registerRoutes(); //
    //     $this->configure();
    //     $this->offerPublishing();
    //     $this->registerLaratrust();
    //     $this->registerCommands();
    // }

    /**
     * Register the service provider.
     *
     * @return void
     */
    // public function register()
    // {
    //     $this->configure();
    //     $this->offerPublishing();
    //     $this->registerLaratrust();
    //     $this->registerCommands();
    // }
    public function register()
    {
        // $this->configure();
        // $this->offerPublishing();
        // $this->registerLaratrust();
        // $this->registerCommands();
        parent::register();
        $this->registerResources();
        $this->registerRoutes();
    }

    public function boot()
    {
        // $this->useMorphMapForRelationships();
        // $this->registerMiddlewares();
        // $this->registerPermissionsToGate();

        // parent::boot();
        $this->registerBladeDirectives();
        $this->defineAssetPublishing();
    }

    /**
     * Register the blade directives.
     *
     * @return void
     */
    private function registerBladeDirectives()
    {
        if (!class_exists('\Blade')) {
            return;
        }

        (new LaratrustRegistersBladeDirectives)->handle($this->app->version());
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

    /**
     * Register the assets that are publishable for the admin panel to work.
     *
     * @return void
     */
    protected function defineAssetPublishing()
    {
        if (!$this->app['config']->get('laratrust.panel.register')) {
            return;
        }
        $providerClass = 'Laratrust\LaratrustServiceProvider';
        $packagePath = $this->getPackagePathFromProvider($providerClass);
        $srcDirectory = "{$packagePath}/laratrust/src";

        $this->publishes([
            $srcDirectory.'/../public' => public_path('laratrust'),
        ], 'laratrust-assets');
    }

    protected function getPackagePathFromProvider($providerClass)
    {
        $reflection = new ReflectionClass($providerClass);
        $filePath = $reflection->getFileName();
        $vendorPath = dirname(dirname(dirname($filePath))); // Navigate to vendor directory
        return $vendorPath;
    }
}
