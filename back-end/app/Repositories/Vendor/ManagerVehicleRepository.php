<?php

namespace App\Repositories\Vendor;

use App\Models\Vehicles;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ManagerVehicleRepository implements ManagerVehicleRepositoryInterface{
    public function showAllVehicles(int $vendorId)
    {
        return Vehicles::where('vendor_id', $vendorId)
                        ->with('coaches.seats') // Lấy luôn thông tin toa và ghế
                        ->get();
    }

    public function create(array $data)
    {
        return Vehicles::create($data);
    }

    public function getByVendor(int $vendorId)
    {
        return Vehicles::where('vendor_id', $vendorId)
            ->with('coaches')
            ->latest()
            ->paginate(10);
    }

    public function update(Vehicles $vehicle, array $data)
    {
        $vehicle->update($data);
        return $vehicle;
    }

    public function delete(Vehicles $vehicle){
        return $vehicle->delete();
    }
}


