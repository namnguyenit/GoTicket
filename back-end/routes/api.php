<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\Vendor\TripController as VendorTripController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\Vendor\DashboardController;
use App\Http\Controllers\Api\Vendor\ManagerVehicleController;
use App\Http\Controllers\Api\Vendor\StopController;
use \App\Http\Controllers\Api\Vendor\TicketController;


use App\Http\Controllers\Api\Admin\DashboardAdminController;
use App\Http\Controllers\Api\Admin\VendorController;




Route::get('/login', function () {

    return response()->json(['message' => 'Not authenticated via web.'], 401);
})->name('login');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::controller(TripController::class)->group(function () {
    Route::get('trips/search', 'search');
    Route::get('trips/{trip}', 'getTripDetail')->whereNumber('trip');
    Route::get('trips/{trip}/stops', 'getTripStops')->whereNumber('trip');
});
Route::get('routes/location', [RouteController::class, 'getAllLocationCity']);

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::middleware('auth:api')->group(function() {
        Route::post('logout', 'logout');
        Route::get('myinfo', 'getInfoAccout');
    });
});








Route::group(['middleware' => ['api', 'auth:api']], function()  {


    Route::group(['prefix' => 'auth'], function() {
        Route::get('myinfo', [AuthController::class, 'getInfoAccout']);
        Route::put('myinfo', [AuthController::class, 'updateProfile']);

    });
    Route::group(['prefix' => 'bookings'], function() {
        Route::post('initiate', [BookingController::class, 'initiate']);
        Route::post('confirm', [BookingController::class, 'confirm']);
    });
    Route::get('trips/{id}/stops', [TripController::class, 'getTripStops']);



    Route::group(['middleware' => 'role:admin', 'prefix' => 'admin'], function() {

        Route::get('/test', function () {
            return response()->json(['message' => 'Chào mừng Admin!']);
        });


        Route::get('/users', [UserController::class, 'getAll']);
        Route::get('/users/search', [UserController::class, 'findByName']);
        Route::get('/users/{email}', [UserController::class, 'findByEmail']);
        Route::put('/users/{email}', [UserController::class, 'update']);
        Route::delete('/users/{email}', [UserController::class, 'delete']);

        Route::get('/dashboard/top-vendors', [DashboardAdminController::class, 'getTopVendors']);

        Route::get('/dashboard/stats', [DashboardAdminController::class, 'getOverallStats']);

        Route::get('vendors/{vendor:user_id}', [App\Http\Controllers\Api\Admin\VendorController::class, 'show']);
        Route::put('vendors/{vendor:user_id}', [App\Http\Controllers\Api\Admin\VendorController::class, 'update']);
        Route::put('vendors/{vendor:user_id}/status', [App\Http\Controllers\Api\Admin\VendorController::class, 'updateStatus']);

        Route::post('vendors', [App\Http\Controllers\Api\Admin\VendorController::class, 'store']);


    });

    Route::middleware('role:admin')->prefix('admin')->group(function() {
        Route::controller(UserController::class)->prefix('users')->group(function() {
            Route::get('/', 'getAll');
            Route::get('/search', 'findByName');
            Route::get('/{user}', 'show')->whereNumber('user');
            Route::put('/{user}', 'updateUser')->whereNumber('user');
            Route::delete('/{user}', 'delete')->whereNumber('user');
        });
    });

    Route::middleware('role:vendor')->prefix('vendor')->group(function() {
        Route::get('dashboard/stats', [DashboardController::class, 'getStats']);
        Route::get('dashboard/info', [DashboardController::class, 'getInfo']);
        Route::post('dashboard/logo', [DashboardController::class, 'uploadLogo']);

        Route::controller(ManagerVehicleController::class)->prefix('vehicles')->group(function() {
            Route::post('/', 'store');//erro

            Route::get('/', 'index');//erro
            Route::get('/{vehicle}', 'show')->whereNumber('vehicle');//erro
            Route::put('/{vehicle}', 'update')->whereNumber('vehicle');//erro
            Route::delete('/{vehicle}', 'destroy')->whereNumber('vehicle');//erro

            Route::post('/{vehicle}/coaches', 'addCoaches')->whereNumber('vehicle');
            Route::delete('/{vehicle}/coaches/{coach}', 'removeCoach')->whereNumber('vehicle')->whereNumber('coach');
        });

        Route::controller(StopController::class)->prefix('stops')->group(function () {
            Route::post('/', 'store');
            Route::get('/', 'index');
            Route::get('/by-location', 'listByLocation');
            Route::get('/location/{location}', 'listByLocationId')->whereNumber('location');
            Route::get('/{stop}', 'show')->whereNumber('stop');
            Route::put('/{stop}', 'update')->whereNumber('stop');
            Route::delete('/{stop}', 'destroy')->whereNumber('stop');
        });

        Route::controller(VendorTripController::class)->prefix('trips')->group(function () {
            Route::post('/', 'store');
            Route::get('/', 'index');
            Route::get('/{trip}', 'show')->whereNumber('trip');
            Route::put('/{trip}', 'update')->whereNumber('trip');
            Route::delete('/{trip}', 'destroy')->whereNumber('trip');
        });

        Route::post('tickets', [TicketController::class, 'store']);
        Route::delete('tickets/{trip}', [TicketController::class, 'destroy'])->whereNumber('trip');

        // Vendor bookings management
        Route::get('bookings', [\App\Http\Controllers\Api\Vendor\BookingController::class, 'index']);
        Route::get('bookings/{booking}', [\App\Http\Controllers\Api\Vendor\BookingController::class, 'show'])->whereNumber('booking');
        Route::put('bookings/{booking}', [\App\Http\Controllers\Api\Vendor\BookingController::class, 'update'])->whereNumber('booking');
        Route::delete('bookings/{booking}', [\App\Http\Controllers\Api\Vendor\BookingController::class, 'destroy'])->whereNumber('booking');
    });
});
