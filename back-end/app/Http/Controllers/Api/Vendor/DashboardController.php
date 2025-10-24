<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|file|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);
        $user = Auth::user();
        $vendor = $user?->vendor;
        if(!$vendor){
            return $this->error(\App\Enums\ApiError::VENDOR_NOT_ASSOCIATED);
        }
        $path = $request->file('logo')->store('vendor-logos', 'public');
        // Build absolute URL using current request host:port (ignore APP_URL in multi-port dev)
        $relative = '/storage/' . ltrim($path, '/');
        $base = rtrim($request->getSchemeAndHttpHost(), '/');
        $absolute = $base . $relative;
        $vendor->logo_url = $absolute;
        $vendor->save();
        return $this->success(['logo_url' => $absolute], ApiSuccess::ACTION_SUCCESS);
    }
}
