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
<<<<<<< HEAD
use App\Http\Controllers\Api\Admin\DashboardAdminController; 
use App\Http\Controllers\Api\Admin\VendorController;




Route::get('/login', function () {
    // Trả về lỗi 401 nếu có ai đó vô tình truy cập route này qua web
    // Hoặc bạn có thể để trống hoặc trả về view nếu muốn
    return response()->json(['message' => 'Not authenticated via web.'], 401);
})->name('login');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
=======
use App\Http\Controllers\Api\Vendor\StopController;
>>>>>>> origin/main




// --- CÁC ROUTE CÔNG KHAI ---
Route::controller(TripController::class)->group(function () {
    Route::get('trips/search', 'search');
    Route::get('trips/{trip}', 'getTripDetail')->whereNumber('trip');
    Route::get('trips/{trip}/stops', 'getTripStops')->whereNumber('trip');
});
Route::get('routes/location', [RouteController::class, 'getAllLocationCity']);


// --- CÁC ROUTE XÁC THỰC ---
Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::middleware('auth:api')->group(function() {
        Route::post('logout', 'logout');
        Route::get('myinfo', 'getInfoAccout');
    });
});



Route::middleware('auth:api')->group(function()  {

<<<<<<< HEAD


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

  

    // Nhóm các route chỉ dành cho ADMIN
    Route::group(['middleware' => 'role:admin', 'prefix' => 'admin'], function() {
        
        Route::get('/test', function () {
            return response()->json(['message' => 'Chào mừng Admin!']);
        });


        Route::get('/users', [UserController::class, 'getAll']);
        Route::get('/users/search', [UserController::class, 'findByName']); // Đặt trước route có tham số
        Route::get('/users/{email}', [UserController::class, 'findByEmail']);
        Route::put('/users/{email}', [UserController::class, 'update']);
        Route::delete('/users/{email}', [UserController::class, 'delete']);

        Route::get('/dashboard/top-vendors', [DashboardAdminController::class, 'getTopVendors']);

        Route::get('/dashboard/stats', [DashboardAdminController::class, 'getOverallStats']);

        Route::get('vendors/{vendor:user_id}', [App\Http\Controllers\Api\Admin\VendorController::class, 'show']);
        Route::put('vendors/{vendor:user_id}', [App\Http\Controllers\Api\Admin\VendorController::class, 'update']);
        Route::put('vendors/{vendor:user_id}/status', [App\Http\Controllers\Api\Admin\VendorController::class, 'updateStatus']); 

        Route::post('vendors', [App\Http\Controllers\Api\Admin\VendorController::class, 'store']);
=======
    // --- USER ROUTES ---
    Route::controller(BookingController::class)->prefix('bookings')->group(function() {
        Route::post('initiate', 'initiate');
        Route::post('confirm', 'confirm');
        // còn thiếu, đợi nâng cấp
>>>>>>> origin/main
    });

    // --- ADMIN ROUTES ---
    Route::middleware('role:admin')->prefix('admin')->group(function() {
        Route::controller(UserController::class)->prefix('users')->group(function() {
            Route::get('/', 'getAll');
            Route::get('/search', 'findByName');
            Route::get('/{user}', 'show')->whereNumber('user');
            Route::put('/{user}', 'updateUser')->whereNumber('user');
            Route::delete('/{user}', 'delete')->whereNumber('user');
        });
    });

    // --- VENDOR ROUTES ---
    Route::middleware('role:vendor')->prefix('vendor')->group(function() {
        Route::get('dashboard/stats', [DashboardController::class, 'getStats']);
        Route::get('dashboard/info', [DashboardController::class, 'getInfo']);

        Route::controller(ManagerVehicleController::class)->prefix('vehicles')->group(function() {
            Route::post('/', 'store');//erro

            Route::get('/', 'index');//erro
            Route::get('/{vehicle}', 'show')->whereNumber('vehicle');//erro
            Route::put('/{vehicle}', 'update')->whereNumber('vehicle');//erro
            Route::delete('/{vehicle}', 'destroy')->whereNumber('vehicle');//erro

            // Manage coaches of a vehicle
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

        // Trips CRUD (vendor)
        Route::controller(VendorTripController::class)->prefix('trips')->group(function () {
            Route::post('/', 'store');
            Route::get('/', 'index');
            Route::get('/{trip}', 'show')->whereNumber('trip');
            Route::put('/{trip}', 'update')->whereNumber('trip');
            Route::delete('/{trip}', 'destroy')->whereNumber('trip');
        });

        // Tickets
        Route::post('tickets', [\App\Http\Controllers\Api\Vendor\TicketController::class, 'store']);
        Route::delete('tickets/{trip}', [\App\Http\Controllers\Api\Vendor\TicketController::class, 'destroy'])->whereNumber('trip');
    });
});
