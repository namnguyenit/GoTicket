<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Vendor\CreateStopRequest;
use App\Http\Resources\Vendor\StopResource;
use App\Services\Vendor\StopService;
use App\Enums\ApiSuccess;
use App\Enums\ApiError;

class StopController extends Controller
{
    protected $stopService;

    public function __construct(StopService $stopService)
    {
        $this->stopService = $stopService;
    }


    public function store(CreateStopRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $stop = $this->stopService->createStop($validatedData);

            return $this->success(new StopResource($stop), ApiSuccess::CREATED_SUCCESS);

        } catch (\Exception $e) {
            return $this->error(ApiError::SERVER_ERROR, $e->getMessage());
        }
    }
}
