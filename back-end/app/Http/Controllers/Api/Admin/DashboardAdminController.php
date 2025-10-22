<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiSuccess;
use App\Services\Admin\DashboardAdminService; // <-- Import service
use Illuminate\Http\Request; // <-- Import request

class DashboardAdminController extends Controller{
    use ResponseHelper;

    protected $dashboardService;


    public function __construct(DashboardAdminService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Lấy danh sách nhà xe có doanh thu cao nhất.
     */
    public function getTopVendors(Request $request)
    {
        // Validate 'limit', mặc định là 5 nếu không có
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:100'
        ]);
        
        $limit = $request->query('limit', 5);

        $data = $this->dashboardService->getTopVendors($limit);

        return $this->success($data, ApiSuccess::GET_DATA_SUCCESS);
    }

    public function getOverallStats()
    {
        $data = $this->dashboardService->getOverallDashboardStats();
        return $this->success($data, ApiSuccess::GET_DATA_SUCCESS);
    }
}