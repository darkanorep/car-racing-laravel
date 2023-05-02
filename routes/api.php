<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::get('api', Controller::class . '@getCars');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    //Moderator end
    Route::group(['prefix' => '/', 'middleware' => ['auth' => 'moderator']], function () {
        Route::get('set-race', ModeratorController::class . '@race');
    });

    //User end
    Route::group(['prefix' => '/'], function () {
        Route::resource('race', UserController::class);
        Route::post('deposit', [UserController::class, 'deposit']);
        Route::get('balance', [UserController::class, 'checkBalance']);
        Route::post('bet/{raceId}', [UserController::class , 'bet']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});