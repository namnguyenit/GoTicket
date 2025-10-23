<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\Vendor\TripController as VendorTripController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\Vendor\DashboardController;
use App\Http\Controllers\Api\Vendor\ManagerVehicleController;
use App\Http\Controllers\Api\Vendor\StopController;
use \App\Http\Controllers\Api\Vendor\TicketController;

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



Route::middleware('auth:api')->group(function()  {

    Route::controller(BookingController::class)->prefix('bookings')->group(function() {
        Route::post('initiate', 'initiate');
        Route::post('confirm', 'confirm');

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
    });
});
