<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Vendor\CreateStopRequest;
use App\Http\Resources\Vendor\StopResource;
use App\Services\Vendor\StopService;
use App\Enums\ApiSuccess;
use App\Enums\ApiError;
use App\Http\Helpers\ResponseHelper;

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
}
