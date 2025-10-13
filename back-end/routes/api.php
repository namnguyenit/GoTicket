<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\Vendor\DashboardController;
use App\Http\Controllers\Api\Vendor\ManagerVehicleController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('trips/search', [TripController::class, 'search']);
Route::get('routes/location', [RouteController::class, 'getAllLocationCity']);

Route::get('trips/{id}', [TripController::class, 'getTripDetail']);

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


    Route::group(['prefix' => 'auth'], function() {
        Route::get('myinfo', [AuthController::class, 'getInfoAccout']);

        
    });
    Route::group(['prefix' => 'bookings'], function() {
        Route::post('initiate', [BookingController::class, 'initiate']);
        Route::post('confirm', [BookingController::class, 'confirm']); 
    });
    Route::get('trips/{id}/stops', [TripController::class, 'getTripStops']);

  

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
        Route::group(['prefix' => 'Tongquan'],function(){
            Route::get('/stats', [DashboardController::class, 'getStats']);
        });

        Route::group(['prefix' => 'Quanlyve'],function(){


        });

        Route::group(['prefix' => 'Quanlyxe'],function(){
             Route::get('/getallverhicel', [ManagerVehicleController::class, 'showAllVerhicel']);
        });
        Route::group(['prefix' => 'Quanlychuyendi'],function(){
            
        });
    });

    // Nhóm các route dành cho cả ADMIN và VENDOR
    Route::group(['middleware' => 'role:admin,vendor'], function() {
        //
       
    });

});