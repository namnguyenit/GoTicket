<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Routes;
use App\Models\VendorRoute;
use App\Models\Vehicles;
use App\Models\Coaches;
use App\Models\Seats;
use App\Models\Trips;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // === TẠO 2 NHÀ XE ===
        $vendors = collect([
            User::firstOrCreate(
                ['email' => 'nhaxephuongtrang@example.com'],
                ['name' => 'Nhà Xe Phương Trang', 'password' => Hash::make('123456'), 'phone_number' => '19006067', 'role' => 'vendor']
            ),
            User::firstOrCreate(
                ['email' => 'nhaxethanhbuoi@example.com'],
                ['name' => 'Nhà Xe Thành Bưởi', 'password' => Hash::make('123456'), 'phone_number' => '19006079', 'role' => 'vendor']
            )
        ])->map(function ($user) {
            return Vendor::firstOrCreate(
                ['user_id' => $user->id],
                ['company_name' => 'Công ty ' . $user->name, 'status' => 'active']
            );
        });

        // === LẤY 4 TUYẾN ĐƯỜNG NGẪU NHIÊN ĐỂ TẠO DỮ LIỆU ===
        // Lấy thêm tuyến Hà Nội - TP.HCM để đảm bảo có dữ liệu cho JSON test của bạn
        $routeHNtoHCM = Routes::whereHas('origin', fn($q) => $q->where('name', 'Hà Nội'))
                              ->whereHas('destination', fn($q) => $q->where('name', 'TP. Hồ Chí Minh'))
                              ->first();
        
        $randomRoutes = Routes::where('id', '!=', $routeHNtoHCM->id)->inRandomOrder()->limit(3)->get()->push($routeHNtoHCM);


        // === TẠO DỮ LIỆU CHUYẾN ĐI CHO TỪNG TUYẾN VÀ TỪNG NHÀ XE ===
        foreach ($randomRoutes as $route) {
            foreach ($vendors as $vendor) {
                // Tạo một vendor route
                $vendorRoute = VendorRoute::create([
                    'vendor_id' => $vendor->id,
                    'route_id' => $route->id,
                    'name' => "Tuyến " . $route->origin->name . " - " . $route->destination->name
                ]);

                // Tạo dữ liệu chuyến đi cho vendor route này
                $this->createTripsForVendorRoute($vendorRoute);
            }
        }
    }

    /**
     * Hàm trợ giúp để tạo xe, ghế, chuyến đi và vé cho một vendor route
     */
    private function createTripsForVendorRoute(VendorRoute $vendorRoute)
    {
        // 1. Tạo phương tiện và xe (coach)
        $vehicle = Vehicles::create([
            'vendor_id' => $vendorRoute->vendor_id,
            'name' => 'Xe giường nằm ' . $vendorRoute->id,
            'vehicle_type' => 'bus',
            'license_plate' => '51A-' . rand(10000, 99999)
        ]);
        $coach = Coaches::create([
            'vehicle_id' => $vehicle->id,
            'identifier' => $vehicle->license_plate,
            'coach_type' => 'sleeper_regular',
            'total_seats' => 40
        ]);

        // 2. Tạo ghế cho xe
        $seats = [];
        for ($i = 1; $i <= 40; $i++) {
            $seats[] = Seats::create(['coach_id' => $coach->id, 'seat_number' => 'A' . $i]);
        }

        // 3. Tạo các chuyến đi cho 7 ngày tới với nhiều khung giờ
        $departureHours = [8, 13, 21]; // 8:00, 13:00, 21:00
        for ($day = 0; $day < 7; $day++) {
            foreach($departureHours as $hour) {
                $basePrice = rand(300, 500) * 1000;
                $departureTime = Carbon::today()->addDays($day)->setTime($hour, 0, 0);
                $arrivalTime = $departureTime->copy()->addHours(rand(28, 36));

                $trip = Trips::create([
                    'vendor_route_id' => $vendorRoute->id,
                    'departure_datetime' => $departureTime,
                    'arrival_datetime' => $arrivalTime,
                    'base_price' => $basePrice,
                    'status' => 'scheduled'
                ]);
                
                $trip->coaches()->attach($coach->id);

                // 4. Mở bán vé
                $tripSeatsData = [];
                foreach ($seats as $seat) {
                    $tripSeatsData[] = [
                        'trip_id' => $trip->id,
                        'seat_id' => $seat->id,
                        'price' => $basePrice,
                        'status' => 'available'
                    ];
                }
                DB::table('trip_seats')->insert($tripSeatsData);
            }
        }
    }
}