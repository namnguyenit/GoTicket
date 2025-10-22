<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Vendor\CreateStopRequest;
use App\Http\Requests\Api\Vendor\UpdateStopRequest;
use App\Http\Resources\Vendor\StopResource;
use App\Services\Vendor\StopService;
use App\Enums\ApiSuccess;
use App\Enums\ApiError;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Models\Stops;
use Illuminate\Support\Facades\Auth;

class StopController extends Controller
{
    protected $stopService;
    use ResponseHelper ;


    public function __construct(StopService $stopService)
    {
        $this->stopService = $stopService;
    }


    public function store(CreateStopRequest $request)
    {
        try {
            $data = $request->validated(); // đã có vendor_id từ prepareForValidation
            $stop = $this->stopService->createStop($data);

            return $this->success(new StopResource($stop), ApiSuccess::CREATED_SUCCESS);

        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }

    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->query('per_page', 10);
            $keyword = $request->query('keyword');
            $paginator = $this->stopService->listStopsByVendor($perPage, $keyword);

            $data = [
                'data' => StopResource::collection($paginator->items()),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ];

            return $this->success($data, ApiSuccess::GET_DATA_SUCCESS);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }

    public function show(Stops $stop)
    {
        try {
            if ($stop->vendor_id !== Auth::user()->vendor->id) {
                return $this->error(ApiError::FORBIDDEN);
            }
            return $this->success(new StopResource($stop), ApiSuccess::GET_DATA_SUCCESS);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }

    public function update(UpdateStopRequest $request, Stops $stop)
    {
        try {
            $data = $request->validated();
            $updated = $this->stopService->updateStop($stop, $data);
            return $this->success(new StopResource($updated), ApiSuccess::ACTION_SUCCESS);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }

    public function destroy(Stops $stop)
    {
        try {
            if ($stop->vendor_id !== Auth::user()->vendor->id) {
                return $this->error(ApiError::FORBIDDEN);
            }
            $this->stopService->deleteStop($stop);
            return $this->success(null, ApiSuccess::ACTION_SUCCESS);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }

    public function listByLocation(Request $request)
    {
        try {
            $keyword = $request->query('keyword');
            $grouped = $this->stopService->listAllStopsGroupedByLocation($keyword);
            return $this->success($grouped, ApiSuccess::GET_DATA_SUCCESS);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }

    public function listByLocationId(Request $request, int $location)
    {
        try {
            $keyword = $request->query('keyword');
            $rows = $this->stopService->listStopsByVendorAndLocation($location, $keyword);
            return $this->success(StopResource::collection($rows), ApiSuccess::GET_DATA_SUCCESS);
        } catch (\Throwable $e) {
            report($e);
            return $this->error(ApiError::SERVER_ERROR, config('app.debug') ? $e->getMessage() : null);
        }
    }
}
