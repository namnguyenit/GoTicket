<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\ResponseHelper;
use App\Services\Vendor\ManagerVehicelService;
use App\Enums\ApiSuccess;

class ManagerVehicleController extends Controller
{
    use ResponseHelper;

    protected $managerVehicelService;

    public function __construct(ManagerVehicelService $managerVehicelService)
    {
        $this->managerVehicelService = $managerVehicelService;
    }


    public function showAllVerhicel(){
        $data = $this->managerVehicelService->getAllvehicel();

        // ✅ Sửa lỗi gõ nhầm: erro -> error
        if ($data->isEmpty()) {
            // Trả về mảng rỗng thay vì báo lỗi
            return $this->success([], ApiSuccess::GET_DATA_SUCCESS);
        }

        return $this->success($data, ApiSuccess::GET_DATA_SUCCESS);
    }
}   
