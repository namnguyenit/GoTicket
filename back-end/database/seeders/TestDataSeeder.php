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
use App\Models\Stops; // <-- THÊM DÒNG NÀY
use App\Models\Trips;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ... (phần tạo nhà xe giữ nguyên)
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

        $routeHNtoHCM = Routes::whereHas('origin', fn($q) => $q->where('name', 'Hà Nội'))
                              ->whereHas('destination', fn($q) => $q->where('name', 'TP. Hồ Chí Minh'))
                              ->first();
        
        if (!$routeHNtoHCM) {
            $this->command->error('Không tìm thấy tuyến đường từ Hà Nội đến TP. Hồ Chí Minh. Vui lòng chạy LocationsSeeder và RoutesSeeder trước.');
            return;
        }

        // ✅ LẤY DANH SÁCH CÁC ĐIỂM DỪNG ĐÃ TẠO
        $pickupStops = Stops::whereIn('name', ['Bến xe Mỹ Đình', 'Bến xe Giáp Bát'])->get();
        $dropoffStops = Stops::whereIn('name', ['Bến xe Miền Đông Mới', 'Văn phòng Sài Gòn'])->get();

        foreach ($vendors as $vendor) {
            $vendorRoute = VendorRoute::create([
                'vendor_id' => $vendor->id,
                'route_id' => $routeHNtoHCM->id,
                'name' => "Tuyến Hà Nội - TP. Hồ Chí Minh (" . $vendor->user->name . ")"
            ]);

            for ($i = 0; $i < 5; $i++) {
                // ✅ TRUYỀN DANH SÁCH ĐIỂM DỪNG VÀO HÀM
                $this->createTripsForVendorRoute($vendorRoute, $pickupStops, $dropoffStops);
            }
        }
    }

    // ✅ CẬP NHẬT THAM SỐ CỦA HÀM
    private function createTripsForVendorRoute(VendorRoute $vendorRoute, $pickupStops, $dropoffStops)
    {
        // ... (phần tạo vehicle, coach, seats giữ nguyên)
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

        $seats = [];
        for ($i = 1; $i <= 40; $i++) {
            $seats[] = Seats::create(['coach_id' => $coach->id, 'seat_number' => 'A' . $i]);
        }

        $departureHours = [8, 13, 21];
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

            // ✅ GÁN ĐIỂM ĐÓN/TRẢ CHO CHUYẾN ĐI
            foreach($pickupStops as $stop) {
                $trip->stops()->attach($stop->id, ['stop_type' => 'pickup', 'scheduled_time' => $departureTime->copy()->addMinutes(rand(0, 30))]);
            }
            foreach($dropoffStops as $stop) {
                $trip->stops()->attach($stop->id, ['stop_type' => 'dropoff', 'scheduled_time' => $arrivalTime->copy()->subMinutes(rand(0, 30))]);
            }

            // ... (phần mở bán vé giữ nguyên)
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