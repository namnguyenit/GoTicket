<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\VendorRoute;
use App\Models\Vehicles;
use App\Models\Coaches;
use App\Models\Seats;
use App\Models\Trips;
use App\Models\Routes;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BulkVendorTripsSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::all();
        if ($vendors->isEmpty()) {
            return;
        }

        $routes = Routes::all();

        foreach ($vendors as $vendor) {
            // Ensure vendor has at least one vendor route
            if ($vendor->vendorRoutes()->count() === 0 && $routes->isNotEmpty()) {
                $route = $routes->random();
                VendorRoute::create([
                    'vendor_id' => $vendor->id,
                    'route_id' => $route->id,
                    'name' => 'Tuyến ' . ($vendor->company_name ?? ('Vendor '.$vendor->id)) . ' ' . $route->id,
                    'is_active' => true,
                ]);
            }

            // Ensure vendor has vehicles for bus and train if missing
            $vehicles = $vendor->vehicles()->get();
            $hasBus = $vehicles->contains('vehicle_type', 'bus');
            $hasTrain = $vehicles->contains('vehicle_type', 'train');

            if (!$hasBus) {
                $bus = Vehicles::create([
                    'vendor_id' => $vendor->id,
                    'name' => 'Xe giường nằm ' . Str::upper(Str::random(4)),
                    'license_plate' => str_pad((string)rand(10,99),2,'0',STR_PAD_LEFT).'A-'.rand(100,999).'.'.rand(10,99),
                    'vehicle_type' => 'bus',
                ]);
                $coach = Coaches::create([
                    'vehicle_id' => $bus->id,
                    'identifier' => 'Coach-1',
                    'coach_type' => 'sleeper_regular',
                    'total_seats' => 40,
                ]);
                for ($s = 1; $s <= 40; $s++) {
                    Seats::create(['coach_id' => $coach->id, 'seat_number' => 'A'.$s]);
                }
            }

            if (!$hasTrain) {
                $train = Vehicles::create([
                    'vendor_id' => $vendor->id,
                    'name' => 'Tàu ' . Str::upper(Str::random(4)),
                    'license_plate' => null,
                    'vehicle_type' => 'train',
                ]);
                $vip = Coaches::create(['vehicle_id'=>$train->id,'identifier'=>'VIP','coach_type'=>'seat_VIP','total_seats'=>24]);
                $soft= Coaches::create(['vehicle_id'=>$train->id,'identifier'=>'SOFT','coach_type'=>'seat_soft','total_seats'=>40]);
                for ($s=1; $s<=24; $s++) { Seats::create(['coach_id'=>$vip->id,'seat_number'=>'V'.$s]); }
                for ($s=1; $s<=40; $s++) { Seats::create(['coach_id'=>$soft->id,'seat_number'=>'S'.$s]); }
            }

            // Refresh vehicles and vendor routes
            $vehicles = $vendor->vehicles()->get();
            $vendorRouteIds = $vendor->vendorRoutes()->pluck('id');
            if ($vendorRouteIds->isEmpty()) {
                // If still empty, skip this vendor
                continue;
            }

            $existingCount = Trips::whereIn('vendor_route_id', $vendorRouteIds)->count();
            $toCreate = max(0, 100 - $existingCount);
            if ($toCreate === 0) {
                continue; // already has at least 100 trips
            }

            $busVehicles = $vehicles->where('vehicle_type', 'bus')->values();
            $trainVehicles = $vehicles->where('vehicle_type', 'train')->values();

            for ($i = 0; $i < $toCreate; $i++) {
                // Alternate vehicle types to diversify results
                $type = ($i % 2 === 0 && $busVehicles->isNotEmpty()) ? 'bus' : 'train';
                if ($type === 'train' && $trainVehicles->isEmpty() && $busVehicles->isNotEmpty()) {
                    $type = 'bus';
                }
                if ($type === 'bus' && $busVehicles->isEmpty() && $trainVehicles->isNotEmpty()) {
                    $type = 'train';
                }

                $vehicle = $type === 'bus' ? $busVehicles->random() : $trainVehicles->random();
                $vendorRouteId = $vendorRouteIds->random();

                $dep = Carbon::today()->addDays($i % 3)->setTime(rand(5, 20), [0,15,30,45][array_rand([0,15,30,45])]);
                $arr = (clone $dep)->addHours(rand(2, 10));
                $price = rand(150, 600) * 1000;

                $trip = Trips::create([
                    'vendor_route_id'    => $vendorRouteId,
                    'departure_datetime' => $dep,
                    'arrival_datetime'   => $arr,
                    'base_price'         => $price,
                    'status'             => 'scheduled',
                ]);

                // Attach coaches of chosen vehicle
                $coachIds = Coaches::where('vehicle_id', $vehicle->id)->pluck('id');
                $order = 1;
                foreach ($coachIds as $cid) {
                    DB::table('trip_coaches')->insert([
                        'trip_id' => $trip->id,
                        'coach_id' => $cid,
                        'coach_order' => $order++,
                    ]);
                }

                // Attach seats with price and availability
                $seats = Seats::whereIn('coach_id', $coachIds)->get();
                foreach ($seats as $seat) {
                    $coach = $seat->coach()->first();
                    $seatPrice = $price;
                    if ($coach && $coach->coach_type === 'seat_VIP') {
                        $seatPrice = $price * 1.3;
                    }
                    DB::table('trip_seats')->insert([
                        'trip_id' => $trip->id,
                        'seat_id' => $seat->id,
                        'price'   => round($seatPrice, 2),
                        'status'  => 'available',
                    ]);
                }
            }
        }
    }
}
