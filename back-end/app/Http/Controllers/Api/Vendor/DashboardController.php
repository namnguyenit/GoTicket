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
}
