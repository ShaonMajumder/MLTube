<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    protected function unauthenticated($request, array $guards)
    {
        if($request->wantsJson() && in_array("sanctum", $guards)){
            $this->message = "Login Required !";
            abort(
                response()->json(
                    [
                        "status" => false, 
                        "message" =>$this->message, 
                        "data" => null
                    ],
                    Response::HTTP_UNAUTHORIZED
                )
            );
        }
        
        throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }
}
