<?php

namespace App\Repositories\Admin;

use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\Bookings; 
use App\Models\BookingDetails;
use App\Models\User;

class DashboardAdminRepository implements DashboardAdminRepositoryInterface
{
    public function getTopVendorsByRevenue(int $limit): Collection
    {
        $now = Carbon::now();

        // Lấy ngày bắt đầu và kết thúc của các khoảng thời gian
        $startOfToday = $now->copy()->startOfDay();
        $endOfToday = $now->copy()->endOfDay();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $now->copy()->endOfWeek(Carbon::SUNDAY);
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        //hàm tính doanh thu 
        $createRevenueSubquery = function ($startDate, $endDate) {
            return function ($query) use ($startDate, $endDate) {
                $query->selectRaw('SUM(total_price)')
                    ->from('bookings')
                    ->where('status', 'confirmed')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('id', function ($subQuery) {
                        $subQuery->select('booking_details.booking_id')
                            ->from('booking_details')
                            ->join('trips', 'booking_details.trip_id', '=', 'trips.id')
                            ->join('vendor_routes', 'trips.vendor_route_id', '=', 'vendor_routes.id')
                            ->whereColumn('vendor_routes.vendor_id', 'vendors.id');
                    });
            };
        };

        //hàm đếm số lượng vé 
        $createTicketCountSubquery = function ($startDate, $endDate) {
            return function ($query) use ($startDate, $endDate) {
                $query->selectRaw('COUNT(*)')
                    ->from('booking_details')
                    ->join('bookings', 'booking_details.booking_id', '=', 'bookings.id')
                    ->join('trips', 'booking_details.trip_id', '=', 'trips.id')
                    ->join('vendor_routes', 'trips.vendor_route_id', '=', 'vendor_routes.id')
                    ->where('bookings.status', 'confirmed')
                    ->whereBetween('bookings.created_at', [$startDate, $endDate])

                    ->whereColumn('vendor_routes.vendor_id', 'vendors.id');
            };
        };

        return Vendor::query()
            ->with('user:id,name,email,phone_number')
            ->select('vendors.id', 'vendors.user_id', 'vendors.company_name', 'vendors.status')
            

            ->selectSub($createRevenueSubquery($startOfWeek, $endOfWeek), 'weekly_revenue')
            ->selectSub($createRevenueSubquery($startOfMonth, $endOfMonth), 'monthly_revenue')
            ->selectSub($createRevenueSubquery($startOfYear, $endOfYear), 'yearly_revenue')


            ->selectSub($createTicketCountSubquery($startOfToday, $endOfToday), 'daily_tickets')
            ->selectSub($createTicketCountSubquery($startOfWeek, $endOfWeek), 'weekly_tickets')
            ->selectSub($createTicketCountSubquery($startOfMonth, $endOfMonth), 'monthly_tickets')
            

            ->orderByDesc('yearly_revenue')
            ->limit($limit)
            ->get();
    }



    public function getOverallStats(): array
    {
        $now = Carbon::now();
        $startOfToday = $now->copy()->startOfDay();
        $endOfToday = $now->copy()->endOfDay();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $now->copy()->endOfWeek(Carbon::SUNDAY);
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $getTotalRevenue = function ($startDate, $endDate) {
            return Bookings::where('status', 'confirmed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_price');
        };

        $getTotalTickets = function ($startDate, $endDate) {
            return BookingDetails::whereHas('booking', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'confirmed')
                      ->whereBetween('created_at', [$startDate, $endDate]);
            })->count();
        };



        $vendorStatuses = Vendor::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();


return [
            'revenue' => [
                'today' => (float) $getTotalRevenue($startOfToday, $endOfToday),
                'this_week' => (float) $getTotalRevenue($startOfWeek, $endOfWeek),
                'this_month' => (float) $getTotalRevenue($startOfMonth, $endOfMonth),
            ],
            'tickets_sold' => [
                'today' => (int) $getTotalTickets($startOfToday, $endOfToday),
                'this_week' => (int) $getTotalTickets($startOfWeek, $endOfWeek),
                'this_month' => (int) $getTotalTickets($startOfMonth, $endOfMonth),
            ],
            'totals' => [
                 'users' => User::count(),
                 'vendors' => Vendor::count(),
            ],
            'vendor_status_distribution' => [ 
                'active' => $vendorStatuses['active'] ?? 0,
                'pending' => $vendorStatuses['pending'] ?? 0,
                'suspended' => $vendorStatuses['suspended'] ?? 0,
            ],
        ];
    }
    
}