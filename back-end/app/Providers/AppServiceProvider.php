<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Services\UserService;
use App\Services\LocationService;
use App\Repositories\LocationRepositoryInterface;
use App\Repositories\LocationRepository;
use App\Repositories\RouteRepository;
use App\Repositories\RouteRepositoryInterface;
use App\Services\RouteService;
use App\Repositories\VendorRoutesRepository;
use App\Repositories\VendorRoutesRepositoryInterface;
use App\Repositories\TripRepository; // Sửa tên file/class cho đúng
use App\Repositories\TripRepositoryInterface;
use App\Repositories\VehiclesRepository;
use App\Repositories\VehiclesRepositoryInterface;
use App\Services\TripService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserService::class); 
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(LocationService::class);
        $this->app->bind(RouteRepositoryInterface::class, RouteRepository::class);
        $this->app->bind(RouteService::class);
        $this->app->bind(VendorRoutesRepositoryInterface::class, VendorRoutesRepository::class);
        $this->app->bind(TripRepositoryInterface::class, TripRepository::class);
        $this->app->bind(VehiclesRepositoryInterface::class, VehiclesRepository::class);
        $this->app->bind(TripService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
