<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiError;
use App\Enums\ApiSuccess;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LocationService;
use App\Http\Helpers\ResponseHelper;
//use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class RouteController extends Controller
{
    use ResponseHelper;


    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }


    public function getAllLocationCity(){
        $location = $this->locationService->getAllLocation();
        return $this->success($location , ApiSuccess::GET_DATA_SUCCESS);
    }

    public function findLocationByName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error(ApiError::VALIDATION_FAILED, $validator->errors());
        }

        
        $name = $request->input('name');

        $location = $this->locationService->findIdBYName($name);

        if (!$location) {
            return $this->error(ApiError::NOT_FOUND);
        }
        $id = $location->id;
        return $this->success(["id :" =>$id], ApiSuccess::GET_DATA_SUCCESS);
    }
}
