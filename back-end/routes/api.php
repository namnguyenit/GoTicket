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
    
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    // Route::get('myinfo', [AuthController::class, 'getInfoAccout']);
});



Route::group(['middleware' => ['api', 'auth:api']], function()  {

    // Route cho các nghiệp vụ xác thực
    Route::group(['prefix' => 'auth'], function() {
        Route::get('myinfo', [AuthController::class, 'getInfoAccout']);
        
    });

    // --- CÁC ROUTE CẦN PHÂN QUYỀN ---

    // Nhóm các route chỉ dành cho ADMIN
    Route::group(['middleware' => 'role:admin', 'prefix' => 'admin'], function() {
        // Ví dụ: Route::get('/users', [UserController::class, 'index']);
        // Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::get('/test', function () {
            return response()->json(['message' => 'Chào mừng Admin!']);
        });
    });

    // Nhóm các route chỉ dành cho NHÀ XE (VENDOR)
    Route::group(['middleware' => 'role:vendor', 'prefix' => 'vendor'], function() {
        // Ví dụ: Route::post('/trips', [TripController::class, 'store']);
        // Route::put('/trips/{id}', [TripController::class, 'update']);
    });

    // Nhóm các route dành cho cả ADMIN và VENDOR
    Route::group(['middleware' => 'role:admin,vendor'], function() {
        // Ví dụ: Route::get('/dashboard-stats', [DashboardController::class, 'getStats']);
       
    });

});