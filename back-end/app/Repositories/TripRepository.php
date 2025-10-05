<?php
namespace App\Repositories;
use App\Models\Trips; // Nhớ import model Trips

class TripRepository implements TripRepositoryInterface
{
    public function search(array $criteria)
    {
        // Bắt đầu một câu truy vấn trên model Trips
        return Trips::query()
            // 1. Lọc theo trạng thái và ngày khởi hành
            ->where('status', 'scheduled')
            ->whereDate('departure_datetime', $criteria['date'])
            
            // 2. Lọc theo điểm đi và điểm đến (dựa vào mối quan hệ đã định nghĩa trong Model)
            ->whereHas('vendorRoute.route', function ($query) use ($criteria) {
                $query->where('origin_location_id', $criteria['origin_id'])
                      ->where('destination_location_id', $criteria['destination_id']);
            })

            // 3. Lọc theo loại xe (bus/train)
            ->whereHas('coaches.vehicle', function ($query) use ($criteria) {
                $query->where('vehicle_type', $criteria['vehicle_type']);
            })

            // 4. Lấy thêm các thông tin liên quan để hiển thị
            ->with([
                'vendorRoute.vendor.user:id,name', // Tên nhà xe
                'coaches'                          // Thông tin xe/toa
            ])
            ->get();
    }
}