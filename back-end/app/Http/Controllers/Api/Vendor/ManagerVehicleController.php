<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Enums\ApiError;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Vendor\CreateVehicleRequest;
use App\Http\Requests\Api\Vendor\UpdateVehicleRequest;
use App\Http\Resources\Vendor\VehicleResource;
use App\Models\Vehicles;
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
    public function store(CreateVehicleRequest $request){
        try{
            $validated = $request->validated();
            $vehicle = $this->managerVehicelService->createVehicle($validated);
            return $this->success(new VehicleResource($vehicle), ApiSuccess::VEHICLE_CREATED);
        } catch (\Exception $e){
            return $this->error(ApiError::SERVER_ERROR, $e->getMessage());
        }
    }

    public function index()
    {
        try{
            $vehicles = $this->managerVehicelService->getVehicleByVendor();
            return $this->success(VehicleResource::collection($vehicles), ApiSuccess::GET_DATA_SUCCESS);
        }catch (\Exception $e){
            return $this->error(ApiError::SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(UpdateVehicleRequest $request, Vehicles $vehicle)
    {
        try{
            $validated = $request->validated();
            $updateVehicle = $this->managerVehicelService->updateVehicle($vehicle, $validated);
            return $this->success(new VehicleResource($updateVehicle), ApiSuccess::VEHICLE_UPDATED);
        }catch (\Exception $e){
            return $this->error(ApiError::SERVER_ERROR, $e->getMessage());
        }
    }

    public function delete(Vehicles $vehicle)
    {
        try{
            if($vehicle->vendor()->id != auth()->user()->vendor->id){
                return $this->error(ApiError::FORBIDDEN);
            }
            $this->managerVehicelService->deleteVehicle($vehicle);
            return $this->success(null, ApiSuccess::VEHICLE_DELETED);
        }catch (\Exception $e){
            return $this->error(ApiError::SERVER_ERROR, $e->getMessage());
        }
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
