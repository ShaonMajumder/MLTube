<?php

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use App\Helpers\MenuManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

    function relativeTime($time) {
        $time = strtotime($time);
        $d[0] = array(1,"second");
        $d[1] = array(60,"minute");
        $d[2] = array(3600,"hour");
        $d[3] = array(86400,"day");
        $d[4] = array(604800,"week");
        $d[5] = array(2592000,"month");
        $d[6] = array(31104000,"year");

        $w = array();

        $return = "";
        $now = time();
        $diff = ($now-$time);
        $secondsLeft = $diff;

        for($i=6;$i>-1;$i--)
        {
            $w[$i] = intval($secondsLeft/$d[$i][0]);
            $secondsLeft -= ($w[$i]*$d[$i][0]);
            if($w[$i]!=0)
            {
                $return.= abs($w[$i]) . " " . $d[$i][1] . (($w[$i]>1)?'s':'') ." ";
            }

        }

        $return .= ($diff>0)?"ago":"left";
        return $return;
    }

    function FirstRelativeTime($time) {
        $time = strtotime($time);
        $d[0] = array(1,"second");
        $d[1] = array(60,"minute");
        $d[2] = array(3600,"hour");
        $d[3] = array(86400,"day");
        $d[4] = array(604800,"week");
        $d[5] = array(2592000,"month");
        $d[6] = array(31104000,"year");

        $w = array();

        $return = "";
        $now = time();
        $diff = ($now-$time);
        $secondsLeft = $diff;

        for($i=6;$i>-1;$i--)
        {
            $w[$i] = intval($secondsLeft/$d[$i][0]);
            $secondsLeft -= ($w[$i]*$d[$i][0]);
            if($w[$i]!=0)
            {
                $return.= abs($w[$i]) . " " . $d[$i][1] . (($w[$i]>1)?'s':'') ." ";
                break;
            }

        }

        $return .= ($diff>0)?"ago":"left";
        return $return;
    }
/*
    function resizeImage($file, $fileNameToStore) {
        // Resize image
        $resize = Image::make($file)->resize(600, null, function ($constraint) {
        $constraint->aspectRatio();
        })->encode('jpg');

        // Create hash value
        $hash = md5($resize->__toString());

        // Prepare qualified image name
        $image = $hash."jpg";

        // Put image to storage
        $save = Storage::put("public/images/{$fileNameToStore}", $resize->__toString());

        if($save) {
        return true;
        }
        return false;
    }
*/


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

if (!function_exists('isActiveRoute')) {
    function isActiveRoute($routeName)
    {
        return request()->routeIs($routeName);
    }
}

if (!function_exists('is_current_route')) {
    function is_current_route($patterns)
    {
        foreach ($patterns as $pattern) {
            if (request()->is($pattern)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('has_any_active_child_route')) {
    function has_any_active_child_route(array $childrens)
    {
        $currentRouteName = strtolower(optional(request()->route())->getName() ?? '');
        foreach ($childrens as $children) {
            if (isset($children['route']) && ($children['route'] === $currentRouteName)) {
                return true;
            }

            if (isset($children['url']) && ($children['url'] === url()->current())) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('is_current_route_name')) {
    function is_current_route_name(string $route)
    {
        $currentRouteName = strtolower(optional(request()->route())->getName() ?? '');
        return $route === $currentRouteName;
    }
}

if (!function_exists('canAccessRouteWithMiddleware')) {
    /**
     * Check if the current user can access a route considering the middleware.
     *
     * @param string $routeName
     * @param array $middleware
     * @return bool
     */
    function canAccessRouteWithMiddleware($routeName, $middleware, $routeParameters = [])
    {
        // Check if the route exists
        if (!Route::has($routeName)) {
            return false;
        }

        // Handle 'auth' middleware specifically
        if (in_array('auth', $middleware) && !Auth::check()) {
            return false;
        }

        // Create a dummy request to simulate access
        $route = Route::getRoutes()->getByName($routeName);
        
        // Apply default parameter values if needed
        $defaults = $route->defaults;
        $parameters = array_merge($defaults, $routeParameters);

        $request = Request::create(route($routeName, $parameters), 'GET');

        foreach ($middleware as $m) {
            $middlewareClass = app('router')->getMiddleware()[$m] ?? null;
            if ($middlewareClass) {
                $middlewareInstance = app($middlewareClass);
                $response = $middlewareInstance->handle($request, function () use ($request) {
                    return $request;
                });

                if ($response instanceof \Illuminate\Http\RedirectResponse || $response instanceof \Symfony\Component\HttpFoundation\Response) {
                    return false;
                }
            }
        }

        return true;
    }
}
?>