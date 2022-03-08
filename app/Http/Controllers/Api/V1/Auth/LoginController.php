<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Components\Message;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    use Message;
    protected $multiple_login = true;
    
    public function login(Request $request){
        try {
            $request->validate([
                "email" => "required", // |exists:users,email
                "password"  => "required"
            ]);

            $user = User::where('email', $request->email)->where("status", true)->first();
            if( empty($user) ){
                return $this->apiOutput(Response::HTTP_FORBIDDEN, "Account Dosen't Exists");
            }else if (!Hash::check($request->password, $user->password)) {
                return $this->apiOutput(Response::HTTP_FORBIDDEN, "Password is Not Match");
            }
            
            if($this->multiple_login){
                // request()->bearerToken()
            }else{
                $user->tokens()->where('tokenable_id', $user->id)->delete();
            }
            
            $this->access_token = $user->createToken( $request->device_name ?? ($request->ip() ?? "Unknown") )->plainTextToken;
            $this->data['profile'] = $user;
            $this->apiSuccess();
            return $this->apiOutput(Response::HTTP_OK, "Login Successfully!");
        } catch (Exception $e) {
            if(!empty($e->validator->errors())){
                return $this->apiOutput(Response::HTTP_FORBIDDEN, $e->validator->errors());    
            }else{
                return $this->apiOutput(Response::HTTP_INTERNAL_SERVER_ERROR, $this->getExceptionError($e));
            }
            
        }
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, "Logout Successfully!");
    }
}
