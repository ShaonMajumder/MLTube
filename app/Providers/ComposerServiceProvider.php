<?php

namespace App\Providers;



use View;
use Illuminate\Support\ServiceProvider;

use App\Models\Channel;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function __construct(){
        //$this->middleware(['auth'])->only('s_shows');
    }

    public function register()
    {
        //
    }

    

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Channel $channel)
    {
        View::composer('app', function($view){
            $view->with('foo', 'channel');
        });
    }

    public function s_shows(Channel $channel){
        
        

        View::composer('app', function($view){
            $view->with('foo', 'channel');
        });
    }
}
