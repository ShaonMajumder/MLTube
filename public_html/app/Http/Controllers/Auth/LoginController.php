<?php

namespace App\Http\Controllers\Auth;

use App\Enums\EntityEnum;
use App\Enums\UserEnum;
use App\Helpers\ErrorMessages;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override the attemptLogin method to include additional checks.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin($request)
    {
        $credentials = $this->credentials($request);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->status === UserEnum::STATUS['INACTIVE'] || $user->status === UserEnum::STATUS['SUSPENDED']) {
                Auth::logout();

                $errorMessage = ErrorMessages::getErrorMessage( EntityEnum::USER, $user->status);

                throw ValidationException::withMessages([
                    $this->username() => [$errorMessage],
                ]);
            }
            
            return true;
        }

        return false;
    }
}
