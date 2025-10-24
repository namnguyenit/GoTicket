<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Routes;
use App\Models\Location;
use App\Models\VendorRoute;
use App\Models\Vehicles;
use App\Models\Coaches;
use App\Models\Seats;
use App\Models\Trips;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HNToNAOct30Seeder extends Seeder
{
    public function run(): void
    {
        $originId = Location::where('name', 'Hà Nội')->value('id');
        $destId   = Location::where('name', 'Nghệ An')->value('id');
        if (!$originId || !$destId) {
            return; // required locations not available
        }

        $route = Routes::firstOrCreate([
            'origin_location_id' => $originId,
            'destination_location_id' => $destId,
        ]);

        $vendors = Vendor::all();
        if ($vendors->isEmpty()) {
            return;
        }

        // Ensure each vendor has a VendorRoute for this specific route and at least one bus/train vehicle with seats
        $vendorBundles = [];
        foreach ($vendors as $vendor) {
            $vr = $vendor->vendorRoutes()->where('route_id', $route->id)->first();
            if (!$vr) {
                $vr = VendorRoute::create([
                    'vendor_id' => $vendor->id,
                    'route_id' => $route->id,
                    'name' => 'HN → NA - ' . ($vendor->company_name ?? ('Vendor '.$vendor->id)),
                    'is_active' => true,
                ]);
            }

            // Vehicles
            $vehicles = $vendor->vehicles()->get();
            $bus = $vehicles->firstWhere('vehicle_type', 'bus');
            $train = $vehicles->firstWhere('vehicle_type', 'train');

            if (!$bus) {
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
                for ($s=1; $s<=40; $s++) { Seats::create(['coach_id'=>$coach->id,'seat_number'=>'A'.$s]); }
            }

            if (!$train) {
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

            $vendorBundles[] = [
                'vendor' => $vendor,
                'vendor_route_id' => $vr->id,
                'bus' => $bus,
                'train' => $train,
            ];
        }

        if (empty($vendorBundles)) return;

        // Count existing trips on 2025-10-30 for this route across vendors
        $vrIds = collect($vendorBundles)->pluck('vendor_route_id')->all();
        $targetDate = Carbon::create(2025, 10, 30, 0, 0, 0, 'UTC');
        $existing = Trips::whereIn('vendor_route_id', $vrIds)
            ->whereDate('departure_datetime', $targetDate->toDateString())
            ->count();

        $toCreate = max(0, 100 - $existing);
        if ($toCreate === 0) return;

        for ($i=0; $i<$toCreate; $i++) {
            $bundle = $vendorBundles[$i % count($vendorBundles)];
            $useBus = ($i % 2 === 0);
            $vehicle = $useBus ? $bundle['bus'] : $bundle['train'];
            if (!$vehicle) { // fallback
                $vehicle = $bundle['bus'] ?? $bundle['train'];
            }

            // Departure time varied within the day (Vietnam time)
            $depLocal = Carbon::create(2025, 10, 30, 5, 0, 0, 'Asia/Ho_Chi_Minh')
                ->addMinutes(rand(0, 15)*15)
                ->addHours(rand(0, 15));
            $arrLocal = (clone $depLocal)->addHours(rand(2, 10));

            $depUtc = $depLocal->copy()->setTimezone('UTC');
            $arrUtc = $arrLocal->copy()->setTimezone('UTC');

            $price = rand(150, 600) * 1000;

            $trip = Trips::create([
                'vendor_route_id'    => $bundle['vendor_route_id'],
                'departure_datetime' => $depUtc,
                'arrival_datetime'   => $arrUtc,
                'base_price'         => $price,
                'status'             => 'scheduled',
            ]);

            // Attach coaches for selected vehicle
            $coachIds = Coaches::where('vehicle_id', $vehicle->id)->pluck('id');
            $order = 1;
            foreach ($coachIds as $cid) {
                DB::table('trip_coaches')->insert([
                    'trip_id' => $trip->id,
                    'coach_id' => $cid,
                    'coach_order' => $order++,
                ]);
            }

            // Attach seats with price
            $seats = Seats::whereIn('coach_id', $coachIds)->get();
            foreach ($seats as $seat) {
                $coach = $seat->coach()->first();
                $seatPrice = $price;
                if ($coach && $coach->coach_type === 'seat_VIP') { $seatPrice = $price * 1.3; }
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
