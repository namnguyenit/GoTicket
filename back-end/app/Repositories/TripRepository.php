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

        $query->where('status', 'scheduled')
              ->whereDate('departure_datetime', $criteria['date'])
              ->whereHas('vendorRoute.route', function ($q) use ($criteria) {
                  $q->where('origin_location_id', $criteria['origin_id'])
                    ->where('destination_location_id', $criteria['destination_id']);
              })
              ->whereHas('coaches.vehicle', function ($q) use ($criteria) {
                  $q->where('vehicle_type', $criteria['vehicle_type']);
              });


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

        return $query
                    ->with([

                        'vendorRoute.vendor.user:id,name',

                        'vendorRoute.route.origin',
                        'vendorRoute.route.destination',

                        'coaches.vehicle',
                    ])

                    ->withCount([
                        'seats as empty_number' => function ($q) {

                            $q->where('trip_seats.status', 'available');
                        },
                    ])
                    ->orderBy('departure_datetime')
                    ->paginate(12); 
    }
}