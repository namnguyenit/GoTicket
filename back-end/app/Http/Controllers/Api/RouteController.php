<?php

namespace App\Http\Controllers\Api;

use App\Enums\ApiError;
use App\Enums\ApiSuccess;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\LocationService;
use App\Services\RouteService; 
use App\Http\Helpers\ResponseHelper;
use App\Http\Requests\Api\SearchRequest; 

class RouteController extends Controller
{
    use ResponseHelper;

    protected $locationService;
    protected $routeService; 

    public function __construct(LocationService $locationService, RouteService $routeService)
    {
        $this->locationService = $locationService;
        $this->routeService = $routeService;
    }

    public function getAllLocationCity(){
        $location = $this->locationService->getAllLocation();
        return $this->success($location , ApiSuccess::GET_DATA_SUCCESS);
    }

    
    public function findRoute(SearchRequest $request)
    {

        $validatedData = $request->validated();
        $originName = $validatedData['origin_location'];
        $destinationName = $validatedData['destination_location']; 

        $route = $this->routeService->findRouteByLocationIds(
            $originName,
            $destinationName
        );


        if (!$route) {
            return $this->error(ApiError::NOT_FOUND, ['message' => 'Không tìm thấy tuyến đường phù hợp.']);
        }

        return $this->success(['route_id' => $route->id], ApiSuccess::GET_DATA_SUCCESS);
    }
}