<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthException;
use App\Response\Status;
use App\Response\Response;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\WalletResource;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(UserRequest $request) {

        $credentials =  new UserResource(User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role' => $request->has('role') ? $request->role : 'user'
        ], Status::OK));

        new WalletResource(Wallet::create([
            'user_id' => $credentials->id,
        ]));

        return $credentials;
    }

    public function login(Request $request) {     

        return auth()->attempt($request->only('username', 'password')) ? 
        response()->json([
            'message' => 'Successfully logged in',
            'username' => auth()->user()->username,
            'token' => auth()->user()->createToken('authToken of '.auth()->user()->username)->plainTextToken
        ], 200) : Response::invalid('credentials' , Status::UNAUTHORIZED);
    }

    public function logout() {
        auth()->user()->tokens()->delete();
        return Response::success('Successfully logged out');
    }
}
