<?php

namespace App\Repositories\Vendor;

use App\Models\Bookings;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DashboardRepository implements DashboardRepositoryInterface
{
    


    public function getRevenueGroupedByPeriod(int $vendorId, Carbon $startDate, Carbon $endDate, string $period): Collection
    {

        $dateColumn = $period === 'day' ? DB::raw('DATE(created_at) as date') : DB::raw('MONTH(created_at) as month');
        $groupByColumn = $period === 'day' ? 'date' : 'month';

        $query = Bookings::query()
            ->where('status', 'confirmed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('details.trip.vendorRoute', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->select(
                $dateColumn, // Lấy ra ngày hoặc tháng
                DB::raw('SUM(total_price) as total_revenue') // Tính tổng doanh thu
            )
            ->groupBy($groupByColumn); // Nhóm kết quả lại

        if ($period === 'month') {
            $query->addSelect(DB::raw('YEAR(created_at) as year'))->groupBy('year');
        }
            
        return $query->orderBy($groupByColumn, 'asc')->get();
    }
}