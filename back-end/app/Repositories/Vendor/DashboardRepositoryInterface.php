<?php

namespace App\Repositories\Vendor;
use Illuminate\Support\Collection;
interface DashboardRepositoryInterface
{
    /**
     * Lấy tổng doanh thu cho một nhà xe trong một khoảng thời gian.
     *
     * @param int $vendorId ID của nhà xe
     * @param \Carbon\Carbon $startDate Ngày bắt đầu
     * @param \Carbon\Carbon $endDate Ngày kết thúc
     * @return float
     */
    
    public function getRevenueGroupedByPeriod(int $vendorId, \Carbon\Carbon $startDate, \Carbon\Carbon $endDate, string $period): Collection;
}