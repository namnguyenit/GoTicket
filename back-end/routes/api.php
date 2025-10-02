<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    // Route cho chức năng đăng ký
    Route::post('register', [AuthController::class, 'register']);

    // (Sau này bạn sẽ thêm các route khác vào đây)
    // Route::post('login', [AuthController::class, 'login']);
    // Route::post('logout', [AuthController::class, 'logout']);
    // Route::post('refresh', [AuthController::class, 'refresh']);
    // Route::get('me', [AuthController::class, 'me']);
});