<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Vehicles;
use App\Models\Coaches;
use App\Models\Seats;
use App\Models\Stops;
use App\Models\Routes;
use App\Models\Location;
use App\Models\VendorRoute;
use App\Models\Trips;
use App\Models\Bookings;
use App\Models\BookingDetails;
use App\Models\Payments;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $vendorUsers = User::where('role', 'vendor')->get();

        if($vendorUsers->isEmpty()){
            for($i=1;$i<=5;$i++){
                $vendorUsers->push(User::create([
                    'name' => 'Nhà xe Demo '.$i,
                    'email' => 'vendor_demo'.$i.'@goticket.com',
                    'password' => bcrypt('password'),
                    'role' => 'vendor',
                ]));
            }
        }
        $customers = User::where('role','customer')->take(10)->get();
        if($customers->count()<10){
            for($i=$customers->count()+1;$i<=10;$i++){
                $customers->push(User::create([
                    'name' => 'Customer '.$i,
                    'email' => 'customer'.$i.'@goticket.com',
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                ]));
            }
        }

        $routes = Routes::all();
        $locations = Location::pluck('id','name');
        $hcm = $locations['Hồ Chí Minh'] ?? null; $hn = $locations['Hà Nội'] ?? null; $dn = $locations['Đà Nẵng'] ?? null; $ld = $locations['Lâm Đồng'] ?? null;

        $tripCount = 0;
        foreach ($vendorUsers as $idx => $vendorUser) {

            $vendor = Vendor::firstOrCreate([
                'user_id' => $vendorUser->id
            ], [
                'company_name' => $vendorUser->name,
                'address' => 'Địa chỉ của ' . $vendorUser->name,
                'status' => 'active',
            ]);

            $numVehicles = rand(2,3);
            $vehicleIds = [];
            for($v=1;$v<=$numVehicles;$v++){
                $type = (rand(0,3)===0) ? 'train' : 'bus';
                $vehicle = Vehicles::create([
                    'vendor_id' => $vendor->id,
                    'name' => ($type==='bus'?'Xe giường nằm ':'Tàu ').Str::upper(Str::random(4)),
                    'license_plate' => $type==='bus' ? (str_pad((string)rand(10,99),2,'0',STR_PAD_LEFT).'A-'.rand(100.100,999).'.'.rand(10,99)) : null,
                    'vehicle_type' => $type,
                ]);
                $vehicleIds[] = $vehicle->id;

                if($type==='bus'){
                    $coach = Coaches::create([
                        'vehicle_id' => $vehicle->id,
                        'identifier' => 'Coach-1',
                        'coach_type' => 'sleeper_regular',
                        'total_seats' => 40,
                    ]);
                    for($s=1;$s<=40;$s++){
                        Seats::create(['coach_id'=>$coach->id,'seat_number'=>'A'.$s]);
                    }
                } else {
                    $vip = Coaches::create(['vehicle_id'=>$vehicle->id,'identifier'=>'VIP','coach_type'=>'seat_VIP','total_seats'=>24]);
                    $soft= Coaches::create(['vehicle_id'=>$vehicle->id,'identifier'=>'SOFT','coach_type'=>'seat_soft','total_seats'=>40]);
                    for($s=1;$s<=24;$s++){ Seats::create(['coach_id'=>$vip->id,'seat_number'=>'V'.$s]); }
                    for($s=1;$s<=40;$s++){ Seats::create(['coach_id'=>$soft->id,'seat_number'=>'S'.$s]); }
                }
            }

            if($hcm){ Stops::firstOrCreate(['vendor_id'=>$vendor->id,'name'=>'VP '.$vendor->company_name.' Sài Gòn'],['location_id'=>$hcm,'address'=>'Q1, TPHCM']); }
            if($dn){ Stops::firstOrCreate(['vendor_id'=>$vendor->id,'name'=>'VP '.$vendor->company_name.' Đà Nẵng'],['location_id'=>$dn,'address'=>'Hải Châu, ĐN']); }
            if($ld){ Stops::firstOrCreate(['vendor_id'=>$vendor->id,'name'=>'VP '.$vendor->company_name.' Đà Lạt'],['location_id'=>$ld,'address'=>'TP Đà Lạt']); }
            if($hn){ Stops::firstOrCreate(['vendor_id'=>$vendor->id,'name'=>'VP '.$vendor->company_name.' Hà Nội'],['location_id'=>$hn,'address'=>'Hoàn Kiếm, HN']); }

            $vendorRoutes = [];
            foreach ($routes->random(min(3, max(1,$routes->count()))) as $r){
                $vendorRoutes[] = VendorRoute::firstOrCreate([
                    'vendor_id'=>$vendor->id,
                    'route_id'=>$r->id
                ],[
                    'name'=>'Tuyến '.$vendor->company_name.' '.$r->id,
                    'is_active'=>true,
                ]);
            }

            foreach($vendorRoutes as $vr){
                $perVR = rand(2,3);
                for($t=0;$t<$perVR;$t++){
                    $dep = Carbon::today()->addDays(rand(-1,7))->setTime(rand(5,20), [0,15,30,45][array_rand([0,15,30,45])]);
                    $arr = (clone $dep)->addHours(rand(2,10));
                    $price = rand(150,600)*1000;
                    $trip = Trips::create([
                        'vendor_route_id'=>$vr->id,
                        'departure_datetime'=>$dep,
                        'arrival_datetime'=>$arr,
                        'base_price'=>$price,
                        'status'=> (rand(0,5)===0)?'cancelled':'scheduled',
                    ]);

                    $vehicleId = $vehicleIds[array_rand($vehicleIds)];
                    $coachIds = Coaches::where('vehicle_id',$vehicleId)->pluck('id');
                    $order=1; foreach($coachIds as $cid){ $trip->coaches()->attach($cid,['coach_order'=>$order++]); }

                    $seats = Seats::whereIn('coach_id',$coachIds)->get();
                    foreach($seats as $seat){
                        $coach = $seat->coach()->first();
                        $seatPrice = $price;
                        if($coach && $coach->coach_type === 'seat_VIP'){ $seatPrice = $price * 1.3; }
                        $trip->seats()->attach($seat->id,['price'=>round($seatPrice,2),'status'=>'available']);
                    }

                    $tripCount++;
                }
            }
        }

        $trips = Trips::with('seats')->inRandomOrder()->limit(20)->get();
        foreach($trips as $trip){
            $toBook = rand(1,3);
            $available = DB::table('trip_seats')->where('trip_id',$trip->id)->where('status','available')->limit($toBook)->get();
            if($available->isEmpty()) continue;
            $customer = $customers->random();
            $total = 0; $seatIds = [];
            foreach($available as $row){ $seatIds[]=$row->seat_id; $total += $row->price; }
            $booking = Bookings::create([
                'user_id'=>$customer->id,
                'trip_id'=>$trip->id,
                'booking_code'=>'GOTICKET-'.strtoupper(Str::random(8)),
                'status'=>'confirmed',
                'total_price'=>$total,
            ]);
            foreach($available as $row){
                BookingDetails::create([
                    'booking_id'=>$booking->id,
                    'trip_id'=>$trip->id,
                    'seat_id'=>$row->seat_id,
                    'price_at_booking'=>$row->price,
                    'pickup_stop_id'=>Stops::inRandomOrder()->value('id'),
                    'dropoff_stop_id'=>Stops::inRandomOrder()->value('id'),
                ]);
            }
            DB::table('trip_seats')->where('trip_id',$trip->id)->whereIn('seat_id',$seatIds)->update(['status'=>'booked']);

            Payments::create([
                'booking_id'=>$booking->id,
                'transaction_id'=>'TX'.strtoupper(Str::random(10)),
                'amount'=>$total,
                'payment_method'=>'card',
                'status'=>'success',
                'paid_at'=>now(),
            ]);
        }


    }
}
