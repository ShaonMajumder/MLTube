<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Components\Message;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
// use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    // use RegistersUsers;
    use Message;

    /**
     * Where to redirect users after registration.
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
        
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function register (Request $request) {
        try {
            
            
            try {
                $request->validate([
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255'],
                    'password' => ['required', 'string', 'min:6', 'confirmed'],
                ]);
            } catch(Exception $e){
                return $this->apiOutput(Response::HTTP_INTERNAL_SERVER_ERROR, 'User Entry Data Format is Invalid');
            }
            
            $input = $request->only(['name', 'email', 'password']);
            $input['password']=Hash::make($input['password']);
            $input['remember_token'] = Str::random(10);
            $user = User::create($input);
            $this->access_token = $user->createToken( $request->device_name ?? ($request->ip() ?? "Unknown") )->plainTextToken; 
            
            
            $this->apiSuccess();
            $this->data = $user;
            return $this->apiOutput(Response::HTTP_OK, "Registration Successfully!");
        } catch (Exception $e) {
            return $this->apiOutput(Response::HTTP_INTERNAL_SERVER_ERROR, $this->getExceptionError($e));
        }
        // if ($validator->fails())
        // {
        //     return response(['errors'=>$validator->errors()->all()], 422);
        // }
    }
}
