<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        /**
         * load the request fields into variable
         */
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);
        /* create user */
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => $fields['password']
        ]);
        /* create token */
        $token = $user->createToken('gaucho')->plainTextToken;
        /* create response */
        $response = [
            'user' => $user,
            'token' => $token
        ];
        /* return to the requestor */
        return response($response, 201);
    }
    public function login(Request $request)
    {
        /**
         * load the request fields into variable
         */
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        /* check email */
        $user = User::where('email', $fields['email'])->first();
        // check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad credentials'
            ], 401);
        }
        /* create token */
        $token = $user->createToken('gaucho')->plainTextToken;
        /* create response */
        $response = [
            'user' => $user,
            'token' => $token
        ];
        /* return to the requestor */
        return response($response, 201);
    }
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'Logged out'
        ];
    }
}
