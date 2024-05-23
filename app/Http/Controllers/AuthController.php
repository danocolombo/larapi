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
            'username' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'sub' => 'required|string|confirmed'
        ]);
        /* create user */
        $user = User::create([
            'username' => $fields['username'],
            'email' => $fields['email'],
            'sub' => $fields['sub']
        ]);
        /* create token */
        $token = $user->createToken('jericho')->plainTextToken;
        /* create response */
        $response = [
            'status' => 200,
            'data' => $user,
            'token' => $token
        ];
        /* return to the requestor */
        return response($response, 201);
    }
    public function ORIGregister(Request $request)
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
            'password' => bcrypt($fields['password'])
        ]);
        /* create token */
        $token = $user->createToken('jericho')->plainTextToken;
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
            'username' => 'required|string',
            'sub' => 'required|string'
        ]);
        /* check email */
        $user = User::where('username', $fields['username'])->first();
        // check password
        // if (!$user || !Hash::check($fields['sub'], $user->sub)) {
        //     return response([
        //         'status' => 401,
        //         'data' => [],
        //         'message' => 'Bad credentials'
        //     ], 401);
        // }
        /* Create token if a matching user is found */
        if ($user) {
            $token = $user->createToken('pate')->plainTextToken;

            /* Create response */
            $response = [
                'status' => 200,
                'data' => $user,
                'token' => $token
            ];

            /* Return the response to the requestor */
            return response($response, 200);
        } else {
            // Handle the case where no matching user is found
            // You might return a 401 Unauthorized response with an error message
            return response(['status' => 404, 'data' => [], 'error' => 'Invalid credentials'], 401);
        }
    }
    public function Origlogin(Request $request)
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
        $token = $user->createToken('pate')->plainTextToken;
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
    public function index()
    {
        return User::all();
    }
}
