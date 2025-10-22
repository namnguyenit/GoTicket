<?php

namespace App\Services\Vendor;

use App\Models\Trips;
use App\Models\Vehicles;
use App\Models\VendorRoute;
use App\Models\Routes as AppRoute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use RuntimeException;

class TicketService
{
    public function createTicket(array $data): Trips
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::user();
            $vendor = $user->vendor ?? null;
            if(!$vendor){
                throw new RuntimeException('VENDOR_NOT_ASSOCIATED');
            }

            $vehicle = Vehicles::query()
                ->where('id', $data['vehicle_id'])
                ->where('vendor_id', $vendor->id)
                ->with(['coaches:id,vehicle_id,identifier,coach_type,total_seats','coaches.seats:id,coach_id,seat_number'])
                ->first();
            if(!$vehicle){
                throw new RuntimeException('VEHICLE_NOT_FOUND');
            }

            $origin = trim((string)$data['from_city']);
            $destination = trim((string)$data['to_city']);

            $originId = DB::table('locations')->whereRaw('LOWER(name) = ?', [mb_strtolower($origin)])->value('id');
            $destId   = DB::table('locations')->whereRaw('LOWER(name) = ?', [mb_strtolower($destination)])->value('id');
            if(!$originId || !$destId){
                throw new RuntimeException('ROUTE_NOT_FOUND');
            }

            $route = AppRoute::firstOrCreate(
                [
                    'origin_location_id' => $originId,
                    'destination_location_id' => $destId,
                ]
            );

            $vendorRoute = VendorRoute::firstOrCreate(
                [
                    'vendor_id' => $vendor->id,
                    'route_id' => $route->id,
                ],
                [
                    'name' => $origin.' - '.$destination,
                    'is_active' => true,
                ]
            );

            // Parse datetimes
            [$dep, $arr] = explode('-', $data['start_time']);
            $depAt = Carbon::parse(($data['start_date'].' '.$dep).':00');
            $arrAt = Carbon::parse(($data['start_date'].' '.$arr).':00');
            if($arrAt->lessThanOrEqualTo($depAt)){
                $arrAt = $arrAt->addDay(); // overnight case
            }

            // Determine pricing for train vs bus
            $isTrain = ($vehicle->vehicle_type === 'train');
            $regularPrice = $isTrain ? (float)$data['regular_price'] : (float)($data['price'] ?? 0);
            $vipPrice     = $isTrain ? (float)$data['vip_price'] : (float)($data['price'] ?? 0);
            $basePrice    = $isTrain ? $regularPrice : (float)($data['price'] ?? 0);

            // Create trip
            $trip = Trips::create([
                'vendor_route_id' => $vendorRoute->id,
                'departure_datetime' => $depAt,
                'arrival_datetime' => $arrAt,
                'base_price' => round($basePrice, 2),
                'status' => 'scheduled',
            ]);

            // Attach coaches of vehicle to trip_coaches in a simple order
            $order = 1;
            foreach ($vehicle->coaches as $coach){
                DB::table('trip_coaches')->insert([
                    'trip_id' => $trip->id,
                    'coach_id' => $coach->id,
                    'coach_order' => $order++,
                ]);
            }

            // Seed trip_seats from coach seats
            foreach ($vehicle->coaches as $coach){
                foreach ($coach->seats as $seat){
                    $seatPrice = $basePrice;
                    if ($isTrain) {
                        $seatPrice = ($coach->coach_type === 'seat_VIP') ? $vipPrice : $regularPrice;
                    }
                    DB::table('trip_seats')->insert([
                        'trip_id' => $trip->id,
                        'seat_id' => $seat->id,
                        'price' => round($seatPrice, 2),
                        'status' => 'available',
                    ]);
                }
            }

            return $trip
                ->load(['vendorRoute.route.origin','vendorRoute.route.destination'])
                ->loadCount(['seats as empty_number' => function($q){
                    $q->where('trip_seats.status', 'available');
                }]);
        });
    }
}
