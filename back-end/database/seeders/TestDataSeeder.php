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

        // === LẤY ĐÚNG TUYẾN HÀ NỘI - TP.HCM ===
        $routeHNtoHCM = Routes::whereHas('origin', fn($q) => $q->where('name', 'Hà Nội'))
                              ->whereHas('destination', fn($q) => $q->where('name', 'TP. Hồ Chí Minh'))
                              ->first();
        
        // Nếu không tìm thấy tuyến, dừng lại để tránh lỗi
        if (!$routeHNtoHCM) {
            $this->command->error('Không tìm thấy tuyến đường từ Hà Nội đến TP. Hồ Chí Minh. Vui lòng chạy LocationsSeeder và RoutesSeeder trước.');
            return;
        }

        // === TẠO DỮ LIỆU CHO CẢ 2 NHÀ XE TRÊN CÙNG 1 TUYẾN ===
        foreach ($vendors as $vendor) {
            // Tạo một vendor route cho nhà xe này
            $vendorRoute = VendorRoute::create([
                'vendor_id' => $vendor->id,
                'route_id' => $routeHNtoHCM->id,
                'name' => "Tuyến Hà Nội - TP. Hồ Chí Minh (" . $vendor->user->name . ")"
            ]);

            // Với mỗi nhà xe, tạo 5 xe khác nhau, mỗi xe chạy 3 chuyến
            for ($i = 0; $i < 5; $i++) {
                $this->createTripsForVendorRoute($vendorRoute);
            }
        }
    }

    /**
     * Hàm trợ giúp để tạo xe, ghế, chuyến đi và vé cho một vendor route
     */
    private function createTripsForVendorRoute(VendorRoute $vendorRoute)
    {
        // 1. Tạo phương tiện và xe (coach) duy nhất cho batch này
        $vehicle = Vehicles::create([
            'vendor_id' => $vendorRoute->vendor_id,
            'name' => 'Xe giường nằm ' . $vendorRoute->id . '-' . rand(1, 100),
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

        // 3. Tạo 3 chuyến đi vào đúng ngày 2025-10-06
        $departureHours = [8, 13, 21]; // 8:00, 13:00, 21:00
        $targetDate = Carbon::createFromFormat('Y-m-d', '2025-10-06')->startOfDay();

        foreach($departureHours as $hour) {
            $basePrice = rand(300, 500) * 1000;
            $departureTime = $targetDate->copy()->setTime($hour, 0, 0);
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