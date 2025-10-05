<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RouteController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('routes/search', [RouteController::class, 'findRoute']);
Route::get('location', [RouteController::class, 'getAllLocationCity']);
 
    // Route::get('myinfo', [AuthController::class, 'getInfoAccout']);


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

  

    // Nhóm các route chỉ dành cho ADMIN
    Route::group(['middleware' => 'role:admin', 'prefix' => 'admin'], function() {
        
        Route::get('/test', function () {
            return response()->json(['message' => 'Chào mừng Admin!']);
        });


        Route::get('/users', [UserController::class, 'getAll']);
        Route::get('/users/search', [UserController::class, 'findByName']); // Đặt trước route có tham số
        Route::get('/users/{email}', [UserController::class, 'findByEmail']);
        Route::put('/users/{email}', [UserController::class, 'updateUser']);
        Route::delete('/users/{email}', [UserController::class, 'delete']);


    });

    // Nhóm các route chỉ dành cho NHÀ XE (VENDOR)
    Route::group(['middleware' => 'role:vendor', 'prefix' => 'vendor'], function() {
       //
    });

    // Nhóm các route dành cho cả ADMIN và VENDOR
    Route::group(['middleware' => 'role:admin,vendor'], function() {
        //
       
    });

});