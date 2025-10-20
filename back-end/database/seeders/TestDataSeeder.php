<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Vehicles;
use App\Models\Stops;
use App\Models\VendorRoute;
use App\Models\Trips;
use App\Models\Bookings;
use Carbon\Carbon;
use Illuminate\Support\Str; // Thêm thư viện Str để tạo mã ngẫu nhiên

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy các user nhà xe
        $vendorUsers = User::where('role', 'vendor')->get();
        $routes = \App\Models\Routes::all();
        $locations = \App\Models\Location::all();

        // Tạo dữ liệu cho từng nhà xe

        foreach ($vendorUsers as $index => $vendorUser) {
            $vendor = Vendor::create([
                'user_id' => $vendorUser->id,
                'company_name' => $vendorUser->name,
                'address' => 'Địa chỉ của ' . $vendorUser->name,
                'email' => strtolower(Str::slug($vendorUser->name)) . '@goticket.vn',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mỗi nhà xe tạo 2 chiếc xe
            $licensePlate1 = '51A-111.0' . ($index + 1);
            $licensePlate2 = '51A-222.0' . ($index + 1);
            Vehicles::create([
                'vendor_id' => $vendor->id,
                'name' => 'Xe giường nằm ' . $licensePlate1,
                'license_plate' => $licensePlate1,
                'vehicle_type' => 'bus',
                'seat_count' => 40,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Vehicles::create([
                'vendor_id' => $vendor->id,
                'name' => 'Xe Limousine ' . $licensePlate2,
                'license_plate' => $licensePlate2,
                'vehicle_type' => 'bus',
                'seat_count' => 28,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Mỗi nhà xe tạo 2 điểm dừng
            Stops::create([
                'name' => 'VP ' . $vendorUser->name . ' Sài Gòn',
                'address' => 'Q1, TPHCM',
                'phone' => '09' . rand(10000000, 99999999),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Stops::create([
                'name' => 'VP ' . $vendorUser->name . ' Đà Lạt',
                'address' => 'TP Đà Lạt',
                'phone' => '09' . rand(10000000, 99999999),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Gán tuyến đường cho nhà xe
            if ($routes->has($index)) {
                $vendorRoute = VendorRoute::create([
                    'vendor_id' => $vendor->id,
                    'route_id' => $routes[$index]->id,
                    'name' => 'Tuyến ' . $vendorUser->name,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Tạo 3 chuyến đi cho tuyến đường đó vào ngày mai
                for ($i = 0; $i < 3; $i++) {
                    Trips::create([
                        'vendor_route_id' => $vendorRoute->id,
                        'departure_datetime' => Carbon::tomorrow()->addHours(7 + $i * 2),
                        'arrival_datetime' => Carbon::tomorrow()->addHours(15 + $i * 2),
                        'base_price' => 350000,
                        'status' => 'active',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Tạo dữ liệu đặt vé
        $customerUser = User::where('role', 'customer')->first();
        $firstTrip = Trips::first();
        if ($customerUser && $firstTrip) {
            Bookings::create([
                'user_id' => $customerUser->id,
                'trip_id' => $firstTrip->id,
                'booking_code' => 'GOTICKET-' . strtoupper(Str::random(8)),
                'status' => 'confirmed',
                'total_price' => 350000 * 2,
                'payment_method' => 'cash',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
