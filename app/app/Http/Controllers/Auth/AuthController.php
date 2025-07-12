<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {        
        $input = $request->validated();
        
        $credentials = [
            'email' => $input['email'],
            'password' => $input['password'],
        ];

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Email ou senha inválidos'], 401);
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user(),
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(Auth::user());
    }
}
