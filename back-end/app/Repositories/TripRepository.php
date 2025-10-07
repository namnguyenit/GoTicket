<?php
namespace App\Repositories;

use App\Models\Trips;
use Illuminate\Database\Eloquent\Builder; // Cần import Builder

class TripRepository implements TripRepositoryInterface
{
    public function search(array $criteria)
    {
        // 1. Bắt đầu câu truy vấn, nhưng chưa thực thi
        $query = Trips::query();

        // --- 2. ÁP DỤNG CÁC BỘ LỌC BẮT BUỘC ---
        $query->where('status', 'scheduled')
              ->whereDate('departure_datetime', $criteria['date'])
              ->whereHas('vendorRoute.route', function ($q) use ($criteria) {
                  $q->where('origin_location_id', $criteria['origin_id'])
                    ->where('destination_location_id', $criteria['destination_id']);
              })
              ->whereHas('coaches.vehicle', function ($q) use ($criteria) {
                  $q->where('vehicle_type', $criteria['vehicle_type']);
              });

        // --- 3. ÁP DỤNG CÁC BỘ LỌC KHÔNG BẮT BUỘC (dùng when()) ---

        // Lọc theo giá tối thiểu
        $query->when($criteria['price_min'] ?? null, function (Builder $q, $priceMin) {
            return $q->where('base_price', '>=', $priceMin);
        });

        // Lọc theo giá tối đa
        $query->when($criteria['price_max'] ?? null, function (Builder $q, $priceMax) {
            return $q->where('base_price', '<=', $priceMax);
        });

        // Lọc theo buổi trong ngày
        $query->when($criteria['time_of_day'] ?? null, function (Builder $q, $timeOfDay) {
            if ($timeOfDay === 'sang') {
                return $q->whereTime('departure_datetime', '>=', '05:00:00')->whereTime('departure_datetime', '<', '12:00:00');
            }
            if ($timeOfDay === 'chieu') {
                return $q->whereTime('departure_datetime', '>=', '12:00:00')->whereTime('departure_datetime', '<', '18:00:00');
            }
            if ($timeOfDay === 'toi') {
                return $q->whereTime('departure_datetime', '>=', '18:00:00')->whereTime('departure_datetime', '<=', '23:59:59');
            }
        });

        // --- 4. CUỐI CÙNG: LẤY KẾT QUẢ VÀ PHÂN TRANG ---
        return $query->with([
                        'vendorRoute.vendor.user:id,name',
                        'coaches'
                    ])
                    ->paginate(12); // Tự động phân trang 12 cái/trang
    }
}