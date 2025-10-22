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
use App\Services\Vendor\ManagerVehicleService;
use App\Enums\ApiSuccess;



class ManagerVehicleController extends Controller
{
    use ResponseHelper;

    protected $managerVehicleService;

    public function __construct(ManagerVehicleService $managerVehicleService)
    {
        $this->managerVehicleService = $managerVehicleService;
    }
    public function store(CreateVehicleRequest $request){
        try{
            $validated = $request->validated();
            $vehicle = $this->managerVehicleService->createVehicle($validated);
            return $this->success(new VehicleResource($vehicle), ApiSuccess::VEHICLE_CREATED);
        } catch (\Exception $e){
            return $this->error(ApiError::SERVER_ERROR, $e->getMessage());
        }
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        try{
            $vehicles = $this->managerVehicleService->getVehicleByVendor();
            return $this->success(VehicleResource::collection($vehicles), ApiSuccess::GET_DATA_SUCCESS);
        }catch (\Exception $e){
            return $this->error(ApiError::SERVER_ERROR, $e->getMessage());
        }
    }

    public function show(Vehicles $vehicle)
    {
        try{
            if ($vehicle->vendor_id != auth()->user()->vendor->id){
                return $this->error(ApiError::FORBIDDEN);
            }
            $vehicle->load('coaches');
            return $this->success(new VehicleResource($vehicle), ApiSuccess::GET_DATA_SUCCESS);
        }catch (\Exception $e){
            return $this->error(ApiError::SERVER_ERROR, $e->getMessage());
        }
    }

    public function update(UpdateVehicleRequest $request, Vehicles $vehicle)
    {
        try{
            $validated = $request->validated();
            $updateVehicle = $this->managerVehicleService->updateVehicle($vehicle, $validated);
            return $this->success(new VehicleResource($updateVehicle), ApiSuccess::VEHICLE_UPDATED);
        }catch (\Exception $e){
            return $this->error(ApiError::SERVER_ERROR, $e->getMessage());
        }
    }

    public function destroy(Vehicles $vehicle)
    {
        try{
            if ($vehicle->vendor_id != auth()->user()->vendor->id){
                return $this->error(ApiError::FORBIDDEN);
            }
            $this->managerVehicleService->deleteVehicle($vehicle);
            return $this->success(null, ApiSuccess::VEHICLE_DELETED);
        }catch (\Exception $e){
            return $this->error(ApiError::SERVER_ERROR, $e->getMessage());
        }
    }


    public function showAllVerhicel(){
        $data = $this->managerVehicleService->getVehicleByVendor();

        // ✅ Sửa lỗi gõ nhầm: erro -> error
        if ($data->isEmpty()) {
            // Trả về mảng rỗng thay vì báo lỗi
            return $this->success([], ApiSuccess::GET_DATA_SUCCESS);
        }

        return $this->success($data, ApiSuccess::GET_DATA_SUCCESS);
    }

}
