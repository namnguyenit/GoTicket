<?php

namespace App\Services\Vendor;

use App\Repositories\Vendor\DashboardRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    protected $dashboardRepository;

    public function __construct(DashboardRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getDashboardStats(): array
    {
        $vendor = Auth::user()->vendor;
        $now = Carbon::now();

        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $now->copy()->endOfWeek(Carbon::SUNDAY);
        
        $weeklyRevenue = [
            'Mon' => 0, 'Tue' => 0, 'Wed' => 0, 'Thu' => 0, 'Fri' => 0, 'Sat' => 0, 'Sun' => 0,
        ];

        $weeklyData = $this->dashboardRepository->getRevenueGroupedByPeriod($vendor->id, $startOfWeek, $endOfWeek, 'day');
        // Map weeklyData if needed

        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        $monthlyRevenue = [
            'Jan' => 0, 'Feb' => 0, 'Mar' => 0, 'Apr' => 0, 'May' => 0, 'Jun' => 0, 
            'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0,
        ];

        $yearlyData = $this->dashboardRepository->getRevenueGroupedByPeriod($vendor->id, $startOfYear, $endOfYear, 'month');
        // Map yearlyData if needed

        return [
            'weekly_revenue_by_day' => $weeklyRevenue,
            'yearly_revenue_by_month' => $monthlyRevenue,
        ];
    }

    public function getVendorInfo(): ?\App\Models\Vendor
    {
        $user = Auth::user();
        if(!$user || !$user->vendor){
            return null;
        }
        return $user->vendor
            ->load(['user'])
            ->loadCount(['vehicles','vendorRoutes']);
    }
}
