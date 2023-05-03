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
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(UserRequest $request) {

        $credentials =  new UserResource(User::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role' => $request->has('role') ? $request->role : 'user'
        ], Status::OK));

        Wallet::create([
            'user_id' => $credentials->id,
        ]);

        return $credentials;
    }

    public function login(Request $request) {
        // if (auth()->attempt($request->only('username', 'password'))) {
        //     return response()->json([
        //         'message' => 'Successfully logged in',
        //         'username' => auth()->user()->username,
        //         'token' => auth()->user()->createToken('authToken of '.auth()->user()->username)->plainTextToken
        //     ], 200);
        // } else {
        //     // return (new Response())->unauthorized(Status::UNAUTHORIZED);
        //     return throw AuthException::unauthorized(Status::UNAUTHORIZED);
        //     // return throw new AuthException('Hello', Status::UNAUTHORIZED);
        // }

        $response = new Response();

        return auth()->attempt($request->only('username', 'password')) ? 
            response()->json([
                'message' => 'Successfully logged in',
                'username' => auth()->user()->username,
                'token' => auth()->user()->createToken('authToken of '.auth()->user()->username)->plainTextToken
            ], 200) : $response->invalid('credentials' , Status::UNAUTHORIZED);
    }

    public function logout() {
        auth()->user()->tokens()->delete();

        return throw AuthException::success(Status::OK);
    }
}
