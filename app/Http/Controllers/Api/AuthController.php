<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['success' => true, 'msg'=>'Successfully logged out', 'data'=>'']);
    }

    protected function respondWithToken($token)
    {
        return response()->json(
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'email' => auth('api')->user()->email
            ]
        );
    }

    public function me(Request $request)
    {
        if(auth('api')->user()==null){
            return response()->json(['success' => false,'msg' => 'Unauthorized', 'data'=>'']);
        }
        return response()->json(['success' => true, 'msg'=>'', 'data'=>auth('api')->user()]);
    }

}
