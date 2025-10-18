<?php

namespace App\Services\Vendor;

use App\Models\Vehicles;
use App\Repositories\Vendor\ManagerVehicelRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



class ManagerVehicelService{
    protected $managervehicleRepository;

    public function __construct(ManagerVehicelRepositoryInterface $managervehicleRepository)
    {
        $this->managervehicleRepository = $managervehicleRepository;
    }


    public function  createVehicle(array $data){
        $user = auth()->user();
        $data['vendor_id'] = $user->vendor->id;

        return $this->managervehicleRepository->create($data);
    }

    public function getVehicleByVendor()
    {
        $vendorID = auth()->user()->vendor->id;
        return $this->managervehicleRepository->getByVendor($vendorID);
    }


    public function updateVehicle(Vehicles $vehicle, array $data)
    {
        return $this->managervehicleRepository->update($vehicle, $data);
    }


    public function deleteVehicle(Vehicles $vehicle){
        return $this->managervehicleRepository->delete($vehicle);
    }


}
