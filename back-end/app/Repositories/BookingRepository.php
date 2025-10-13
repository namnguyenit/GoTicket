<?php

namespace App\Repositories;

use App\Models\Bookings;
use App\Models\TripSeats;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingRepository implements BookingRepositoryInterface
{
    public function create(array $bookingData, array $seatDetails): Bookings
    {
        return DB::transaction(function () use ($bookingData, $seatDetails) {
            // 1. Tạo bản ghi `bookings`
            $booking = Bookings::create([
                'user_id' => $bookingData['user_id'],
                'booking_code' => 'BK-' . strtoupper(Str::random(8)),
                'total_price' => $bookingData['total_price'],
                'status' => 'confirmed',
            ]);

            // 2. Lặp qua các ghế và tạo bản ghi `booking_details`
            $tripId = null;
            foreach ($seatDetails as $seat) {
                // ✅ SỬA LẠI KHỐI NÀY
                $booking->details()->create([
                    'trip_id' => $seat['trip_id'],
                    'seat_id' => $seat['id'],
                    'price_at_booking' => $seat['price'],
                    'pickup_stop_id' => $bookingData['pickup_stop_id'],   // Thêm dòng này
                    'dropoff_stop_id' => $bookingData['dropoff_stop_id'], // Thêm dòng này
                ]);
                $tripId = $seat['trip_id'];
            }

            // 3. Cập nhật trạng thái ghế
            $seatIds = array_column($seatDetails, 'id');
            TripSeats::where('trip_id', $tripId)
                ->whereIn('seat_id', $seatIds)
                ->update(['status' => 'booked']);

            return $booking;
        });
    }
}