<?php
namespace App\Repositories;

use App\Models\Trips;
use Illuminate\Database\Eloquent\Builder; 

class TripRepository implements TripRepositoryInterface
{
    public function findById(int $id): ?Trips
    {

        return Trips::with([
            'vendorRoute.vendor.user:id,name', 
            'coaches.seats', 
            'seats' 
        ])->find($id);
    }
    
     public function findWithStops(int $id): ?Trips
    {


        return Trips::with('stops')->find($id);
    }
    
    public function search(array $criteria)
    {

        $query = Trips::query();

        $query->where('status', 'scheduled');

        if (!empty($criteria['date'])) {
            $query->whereDate('departure_datetime', $criteria['date']);
        }
        if (!empty($criteria['origin_id']) && !empty($criteria['destination_id'])) {
            $query->whereHas('vendorRoute.route', function ($q) use ($criteria) {
                $q->where('origin_location_id', $criteria['origin_id'])
                  ->where('destination_location_id', $criteria['destination_id']);
            });
        }
        if (!empty($criteria['vehicle_type'])) {
            $query->whereHas('coaches.vehicle', function ($q) use ($criteria) {
                $q->where('vehicle_type', $criteria['vehicle_type']);
            });
        }


        $query->when($criteria['price_min'] ?? null, function (Builder $q, $priceMin) {
            return $q->where('base_price', '>=', $priceMin);
        });

        $query->when($criteria['price_max'] ?? null, function (Builder $q, $priceMax) {
            return $q->where('base_price', '<=', $priceMax);
        });

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

        if (!empty($criteria['time_slots']) && is_array($criteria['time_slots'])) {
            $query->where(function (Builder $q) use ($criteria) {
                foreach ($criteria['time_slots'] as $slot) {
                    if (preg_match('/^(\d{2}:\d{2})-(\d{2}:\d{2})$/', $slot, $m)) {
                        [$all, $from, $to] = $m;
                        $q->orWhere(function (Builder $qq) use ($from, $to) {
                            $qq->whereTime('departure_datetime', '>=', $from)
                               ->whereTime('departure_datetime', '<=', $to);
                        });
                    }
                }
            });
        }

        if (!empty($criteria['coach_types']) && ($criteria['vehicle_type'] ?? 'bus') === 'bus') {
            $query->whereHas('coaches', function (Builder $q) use ($criteria) {
                $q->whereIn('coach_type', $criteria['coach_types']);
            });
        }

        return $query
                    ->with([
                        'vendorRoute.vendor.user:id,name',
                        'vendorRoute.route.origin',
                        'vendorRoute.route.destination',
                        'coaches.vehicle',
                    ])
                    ->when(($criteria['vehicle_type'] ?? null) === 'train', function ($q) {
                        return $q->with([
                            'coaches:id,vehicle_id,identifier,coach_type,total_seats',
                            'coaches.seats:id,coach_id,seat_number',
                        ]);
                    })
                    ->withCount([
                        'seats as empty_number' => function ($q) {
                            $q->where('trip_seats.status', 'available');
                        },
                    ])
                    ->orderBy('departure_datetime')
                    ->paginate($criteria['per_page'] ?? 12); 
    }
}