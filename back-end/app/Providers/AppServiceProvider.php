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
use App\Repositories\TripRepository;
use App\Repositories\TripRepositoryInterface;
use App\Repositories\VehiclesRepository;
use App\Repositories\VehiclesRepositoryInterface;
use App\Services\TripService;
use App\Repositories\BookingRepository;
use App\Repositories\BookingRepositoryInterface;
use App\Services\BookingService;
use App\Repositories\Vendor\DashboardRepository;
use App\Repositories\Vendor\DashboardRepositoryInterface;
use App\Services\Vendor\DashboardService;

use App\Repositories\Vendor\ManagerVehicelRepository;
use App\Repositories\Vendor\ManagerVehicelRepositoryInterface;
use App\Services\Vendor\ManagerVehicelService;
use App\Repositories\Admin\DashboardAdminRepository;
use App\Repositories\Admin\DashboardAdminRepositoryInterface;
use App\Services\Admin\DashboardAdminService;

use App\Repositories\Vendor\ManagerVehicleRepository;
use App\Repositories\Vendor\ManagerVehicleRepositoryInterface;
use App\Services\Vendor\ManagerVehicleService;


use App\Repositories\Vendor\StopRepository;
use App\Repositories\Vendor\StopRepositoryInterface;
use App\Services\Vendor\StopService;


use App\Repositories\Vendor\CoachRepository;
use App\Repositories\Vendor\CoachRepositoryInterface;


use App\Repositories\Vendor\SeatRepository;
use App\Repositories\Vendor\SeatRepositoryInterface;



class AppServiceProvider extends ServiceProvider
{
    
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
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(BookingService::class);
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
        $this->app->bind(DashboardService::class);

        $this->app->bind(ManagerVehicelRepositoryInterface::class , ManagerVehicelRepository::class);
        $this->app->bind(ManagerVehicelService::class);
        $this->app->bind(DashboardAdminRepositoryInterface::class , DashboardAdminRepository::class);
        $this->app->bind(DashboardAdminService::class);

        $this->app->bind(ManagerVehicleRepositoryInterface::class , ManagerVehicleRepository::class);
        $this->app->bind(ManagerVehicleService::class);


        $this->app->bind(StopRepositoryInterface::class, StopRepository::class);
        $this->app->bind(StopService::class);

        $this->app->bind(CoachRepositoryInterface::class, CoachRepository::class);

        $this->app->bind(SeatRepositoryInterface::class, SeatRepository::class);

    }

    
    public function boot(): void
    {

    }
}
