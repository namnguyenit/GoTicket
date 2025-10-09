<?php

namespace App\Services\Vendor;

use App\Repositories\Vendor\ManagerVehicleRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



class ManagerVehicleService{
    protected $managervehicleRepository;

    public function __construct(ManagerVehicleRepositoryInterface $managervehicleRepository)
    {
        $this->managervehicleRepository = $$managervehicleRepository;
    }


    public function getAllvehicle(){
        
    }
}