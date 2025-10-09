<?php

namespace App\Services\Vendor;

use App\Repositories\Vendor\ManagerVehicelRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



class ManagerVehicelService{
    protected $managervehicleRepository;

    public function __construct(ManagerVehicelRepositoryInterface $managervehicleRepository)
    {
        $this->managervehicleRepository = $managervehicleRepository;
    }


    public function getAllvehicel()
    {
        // Lấy thông tin nhà xe đang đăng nhập
        $vendor = Auth::user()->vendor;
        
        // Gọi hàm đã sửa ở Repository
        return $this->managervehicleRepository->allForVendor($vendor->id);
    }
}