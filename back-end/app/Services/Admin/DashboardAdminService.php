<?php

namespace App\Services\Admin;

use App\Repositories\Admin\DashboardAdminRepositoryInterface;

class DashboardAdminService
{
    protected $dashboardRepository;

    public function __construct(DashboardAdminRepositoryInterface $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getTopVendors(int $limit)
    {
        $vendors = $this->dashboardRepository->getTopVendorsByRevenue($limit);

        
        return $vendors->map(function ($vendor) {
            return [
                'vendor_info' => [
                    'id' => $vendor->id,
                    'company_name' => $vendor->company_name,
                    'status' => $vendor->status,
                    'contact_name' => $vendor->user->name,
                    'email' => $vendor->user->email,
                    'phone_number' => $vendor->user->phone_number,
                ],
                'revenue' => [
        
                    'this_week' => (float) ($vendor->weekly_revenue ?? 0),
                    'this_month' => (float) ($vendor->monthly_revenue ?? 0),
                    'this_year' => (float) ($vendor->yearly_revenue ?? 0),
                ],
        
                'tickets_sold' => [
                    'today' => (int) ($vendor->daily_tickets ?? 0),
                    'this_week' => (int) ($vendor->weekly_tickets ?? 0),
                    'this_month' => (int) ($vendor->monthly_tickets ?? 0),
                ]
            ];
        });
    }
    public function getOverallDashboardStats(): array
    {
        return $this->dashboardRepository->getOverallStats();
    }
}