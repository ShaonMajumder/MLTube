<?php

namespace App\Http\Controllers\Api\V1;

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
    
    public function login(Request $request)
    {
        try {
            $request->validate([
                "email" => "required",
                "password"  => "required"
            ]);

            $user = User::where('email', $request->email)->where("status", true)->first();
            
            if( empty($user) ){
                return $this->apiOutput(Response::HTTP_FORBIDDEN, "Account Dosen't Exists");
            }

            if (!Hash::check($request->password, $user->password)) {
                return $this->apiOutput(Response::HTTP_FORBIDDEN, "Password is Not Match");
            }

            $this->access_token = $user->createToken( $request->device_name ?? ($request->ip() ?? "Unknown") )->plainTextToken; 
            
            $this->apiSuccess();
            $this->data["driver"] = $user->id;
            return $this->apiOutput(Response::HTTP_OK, "Login Successfully!");

        } catch (Exception $e) {
            return $this->apiOutput(Response::HTTP_INTERNAL_SERVER_ERROR, $this->getExceptionError($e));
        }
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();
        $this->apiSuccess();
        return $this->apiOutput(Response::HTTP_OK, "Logout Successfully!");
    }
}
