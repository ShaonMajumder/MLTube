<?php

use App\Helpers\MenuManager;

if (!function_exists('menu')) {
    function menu()
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new MenuManager();
        }

        return $instance;
    }
}
