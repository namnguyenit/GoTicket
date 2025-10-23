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
            if (empty($seatDetails)) {
                throw new \InvalidArgumentException('Seat details required');
            }
            $first = $seatDetails[0];
            $tripId = $first['trip_id'] ?? ($bookingData['trip_id'] ?? null);
            if (!$tripId) {
                throw new \InvalidArgumentException('trip_id missing');
            }

            $booking = Bookings::create([
                'user_id' => $bookingData['user_id'],
                'trip_id' => $tripId,
                'booking_code' => 'BK-' . strtoupper(Str::random(8)),
                'total_price' => $bookingData['total_price'],
                'status' => 'confirmed',
            ]);

            foreach ($seatDetails as $seat) {
                $booking->details()->create([
                    'trip_id' => $seat['trip_id'],
                    'seat_id' => $seat['id'],
                    'price_at_booking' => $seat['price'],
                    'pickup_stop_id' => $bookingData['pickup_stop_id'],
                    'dropoff_stop_id' => $bookingData['dropoff_stop_id'],
                ]);
            }

            $seatIds = array_column($seatDetails, 'id');
            TripSeats::where('trip_id', $tripId)
                ->whereIn('seat_id', $seatIds)
                ->update(['status' => 'booked']);

            return $booking;
        });
    }
}