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

    // --- Thống kê doanh thu theo ngày trong tuần hiện tại ---
    $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY); // Bắt đầu từ T2
    $endOfWeek = $now->copy()->endOfWeek(Carbon::SUNDAY);   // Kết thúc vào CN
    
    // Tạo một mảng doanh thu mặc định cho tuần, tất cả đều bằng 0
    $weeklyRevenue = [
        'Mon' => 0, 'Tue' => 0, 'Wed' => 0, 'Thu' => 0, 'Fri' => 0, 'Sat' => 0, 'Sun' => 0,
    ];

    // Lấy dữ liệu thực tế từ CSDL
    $weeklyData = $this->dashboardRepository->getRevenueGroupedByPeriod($vendor->id, $startOfWeek, $endOfWeek, 'day');
    
    // Cập nhật doanh thu vào mảng
    // foreach ($weeklyData as $data) {
    //     $dayName = Carbon::parse($data->date)->format('D'); // Lấy tên viết tắt của ngày (Mon, Tue...)
    //     $weeklyRevenue[$dayName] = (float) $data->total_revenue;
    // }

    // --- Thống kê doanh thu theo tháng trong năm hiện tại ---
    $startOfYear = $now->copy()->startOfYear();
    $endOfYear = $now->copy()->endOfYear();

    // Tạo mảng doanh thu mặc định cho năm
    $monthlyRevenue = [
        'Jan' => 0, 'Feb' => 0, 'Mar' => 0, 'Apr' => 0, 'May' => 0, 'Jun' => 0, 
        'Jul' => 0, 'Aug' => 0, 'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0,
    ];

    // Lấy dữ liệu thực tế từ CSDL
    $yearlyData = $this->dashboardRepository->getRevenueGroupedByPeriod($vendor->id, $startOfYear, $endOfYear, 'month');

    // Cập nhật doanh thu vào mảng
    // foreach ($yearlyData as $data) {
    //     $monthName = Carbon::create()->month($data->month)->format('M'); // Lấy tên viết tắt của tháng (Jan, Feb...)
    //     $monthlyRevenue[$monthName] = (float) $data->total_revenue;
    // }

    return [
        'weekly_revenue_by_day' => $weeklyRevenue,
        'yearly_revenue_by_month' => $monthlyRevenue,
    ];
}
}