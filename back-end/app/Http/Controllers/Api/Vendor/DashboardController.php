<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiSuccess;
use App\Services\Vendor\DashboardService;


class DashboardController extends Controller
{
    use ResponseHelper;

    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getStats()
    {
        $stats = $this->dashboardService->getDashboardStats();
        return $this->success($stats, ApiSuccess::GET_DATA_SUCCESS);
    }

    public function getInfo()
    {
        $vendor = $this->dashboardService->getVendorInfo();
        if(!$vendor){
            return $this->error(\App\Enums\ApiError::VENDOR_NOT_ASSOCIATED);
        }
        return $this->success(new \App\Http\Resources\Vendor\VendorResource($vendor), ApiSuccess::GET_DATA_SUCCESS);
    }
}
