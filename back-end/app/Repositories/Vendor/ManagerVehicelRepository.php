<?php

namespace App\Repositories\Vendor;

use App\Models\Vehicles;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;


class ManagerVehicelRepository implements ManagerVehicelRepositoryInterface{
    public function allForVendor(int $vendorId)
    {
        return Vehicles::where('vendor_id', $vendorId)
                        ->with('coaches.seats') // Lấy luôn thông tin toa và ghế
                        ->get();        
    }
}