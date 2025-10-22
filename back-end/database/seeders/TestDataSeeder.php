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
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy các user nhà xe, khách hàng và các dữ liệu nền tảng đã được tạo từ các Seeder trước
        $vendorUsers = User::where('role', 'vendor')->get();
        $customerUser = User::where('role', 'customer')->first();
        $routes = Routes::all();
        $locations = Location::all();

        // Lấy ID cụ thể của HCM và Đà Lạt để dùng cho điểm dừng, đảm bảo tính chính xác
        $locationHCMId = $locations->firstWhere('name', 'Hồ Chí Minh')->id;
        $locationDLId = $locations->firstWhere('name', 'Lâm Đồng')->id;

        // Tạo dữ liệu chi tiết cho từng nhà xe
        foreach ($vendorUsers as $index => $vendorUser) {
            // 1. TẠO NHÀ XE (VENDOR)
            $vendor = Vendor::create([
                'user_id' => $vendorUser->id,
                'company_name' => $vendorUser->name,
                'address' => 'Địa chỉ của ' . $vendorUser->name,
                'status' => 'active',
            ]);

            // 2. TẠO XE (VEHICLE)
            $vehicle = Vehicles::create([
                'vendor_id' => $vendor->id,
                'name' => 'Xe giường nằm ' . '51A-111.0' . ($index + 1),
                'license_plate' => '51A-111.0' . ($index + 1),
                'vehicle_type' => 'bus',
            ]);
            
            // 2.1. TẠO TOA XE (COACH) VÀ GHẾ (SEATS) CHO XE VỪA TẠO
            $coach = Coaches::create([
                'vehicle_id' => $vehicle->id,
                'identifier' => 'Tầng 1',
                'coach_type' => 'sleeper_regular',
                'total_seats' => 40,
            ]);

            $createdSeats = [];
            for ($s = 1; $s <= 40; $s++) {
                $createdSeats[] = Seats::create([
                    'coach_id' => $coach->id,
                    'seat_number' => 'A' . $s,
                ]);
            }

            // 3. TẠO CÁC ĐIỂM DỪNG (STOPS)
            $stopHCM = Stops::create([
                'vendor_id' => $vendor->id,
                'location_id' => $locationHCMId,
                'name' => 'VP ' . $vendorUser->name . ' Sài Gòn',
                'address' => 'Q1, TPHCM',
            ]);
            $stopDL = Stops::create([
                'vendor_id' => $vendor->id,
                'location_id' => $locationDLId,
                'name' => 'VP ' . $vendorUser->name . ' Đà Lạt',
                'address' => 'TP Đà Lạt',
            ]);

            // 4. GÁN TUYẾN ĐƯỜNG CHO NHÀ XE (VENDOR ROUTE)
            // Lấy route tương ứng từ RoutesSeeder một cách an toàn
            if ($routes->has($index)) {
                $vendorRoute = VendorRoute::create([
                    'vendor_id' => $vendor->id,
                    'route_id' => $routes[$index]->id,
                    'name' => 'Tuyến ' . $vendorUser->name,
                    'is_active' => true,
                ]);

                // 5. TẠO CÁC CHUYẾN ĐI (TRIPS)
                for ($i = 0; $i < 3; $i++) {
                    $trip = Trips::create([
                        'vendor_route_id' => $vendorRoute->id,
                        'departure_datetime' => Carbon::tomorrow()->addHours(7 + $i * 2),
                        'arrival_datetime' => Carbon::tomorrow()->addHours(15 + $i * 2),
                        'base_price' => 350000,
                        'status' => 'scheduled',
                    ]);

                    // 5.1. GÁN XE VÀO CHUYẾN ĐI (bảng trip_coaches)
                    $trip->coaches()->attach($coach->id, ['coach_order' => 1]);

                    // 5.2. TẠO KHO VÉ CHO CHUYẾN ĐI (bảng trip_seats)
                    foreach ($createdSeats as $seat) {
                        $trip->seats()->attach($seat->id, [
                            'price' => $trip->base_price,
                            'status' => 'available'
                        ]);
                    }
                }
            }
        }

        // 6. TẠO MỘT LƯỢT ĐẶT VÉ MẪU
        $firstTrip = Trips::first();
        if ($customerUser && $firstTrip) {
            // Lấy 2 ghế đầu tiên còn trống của chuyến để đặt
            $seatsToBook = DB::table('trip_seats')
                ->where('trip_id', $firstTrip->id)
                ->where('status', 'available')
                ->join('seats', 'trip_seats.seat_id', '=', 'seats.id')
                ->select('trip_seats.seat_id', 'trip_seats.price')
                ->limit(2)
                ->get();

            if($seatsToBook->count() == 2) {
                $totalPrice = $seatsToBook->sum('price');
                $pickupStopId = Stops::first()->id;
                $dropoffStopId = Stops::skip(1)->first()->id;
                
                // TẠO BOOKING - ĐÃ SỬA LỖI
                $booking = Bookings::create([
                    'user_id' => $customerUser->id,
                    'trip_id' => $firstTrip->id, // <-- THÊM DÒNG NÀY ĐỂ KHỚP VỚI MIGRATION
                    'booking_code' => 'GOTICKET-' . strtoupper(Str::random(8)),
                    'status' => 'confirmed',
                    'total_price' => $totalPrice,
                ]);

                // Tạo chi tiết booking cho từng ghế
                foreach($seatsToBook as $seat) {
                    BookingDetails::create([
                        'booking_id' => $booking->id,
                        'trip_id' => $firstTrip->id,
                        'seat_id' => $seat->seat_id,
                        'price_at_booking' => $seat->price,
                        'pickup_stop_id' => $pickupStopId,
                        'dropoff_stop_id' => $dropoffStopId,
                    ]);
                }
                
                // Cập nhật trạng thái ghế đã đặt
                DB::table('trip_seats')
                    ->where('trip_id', $firstTrip->id)
                    ->whereIn('seat_id', $seatsToBook->pluck('seat_id'))
                    ->update(['status' => 'booked']);
            }
        }
    }
}