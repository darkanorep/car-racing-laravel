<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function register(UserRequest $request) {

        $credentials =  new UserResource(User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role' => $request->has('role') ? $request->role : 'user'
        ]));

        Wallet::create([
            'user_id' => $credentials->id,
        ]);

        return $credentials;
    }

    public function login(Request $request) {
        if (auth()->attempt($request->only('username', 'password'))) {
            return response()->json([
                'message' => 'Successfully logged in',
                'username' => auth()->user()->username,
                'token' => auth()->user()->createToken('authToken of '.auth()->user()->username)->plainTextToken
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid credentials.'
            ]);
        }
    }

    public function logout() {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }
}
