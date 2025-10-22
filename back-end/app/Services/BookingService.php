<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\BookingRepositoryInterface;
use App\Repositories\TripRepositoryInterface;
use Illuminate\Validation\ValidationException;

class BookingService
{
    protected $tripRepository;
    protected $bookingRepository;

    public function __construct(
        TripRepositoryInterface $tripRepository,
        BookingRepositoryInterface $bookingRepository
    ) {
        $this->tripRepository = $tripRepository;
        $this->bookingRepository = $bookingRepository;
    }

    
    public function initiateBooking(int $tripId, array $seatIds, User $user): array
    {

        $trip = $this->tripRepository->findById($tripId);

        if (!$trip || $trip->status !== 'scheduled') {
            throw ValidationException::withMessages([
                'trip_id' => 'Chuyến đi không hợp lệ hoặc không còn tồn tại.'
            ]);
        }

        $tripSeatsMap = $trip->seats->keyBy('id');
        
        $totalPrice = 0;
        $selectedSeatsInfo = [];
        $unavailableSeats = [];

        foreach ($seatIds as $seatId) {
            $seat = $tripSeatsMap->get($seatId);

            if (!$seat || $seat->pivot->status !== 'available') {
                $unavailableSeats[] = $seatId;
                continue;
            }

            $totalPrice += $seat->pivot->price;
            $selectedSeatsInfo[] = [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'price' => $seat->pivot->price,
            ];
        }

        if (!empty($unavailableSeats)) {
            throw ValidationException::withMessages([
                'seat_ids' => 'Một hoặc nhiều ghế bạn chọn không hợp lệ hoặc đã được đặt.',
                'unavailable_seats' => $unavailableSeats
            ]);
        }

        return [
        'user_info' => [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
        ],
        'trip_info' => [
            'id' => $trip->id,






            'vendor_name' => $trip->vendorRoute?->vendor?->user?->name ?? 'Không xác định',

            'departure_datetime' => $trip->departure_datetime,
            'arrival_datetime' => $trip->arrival_datetime,
        ],
        'booking_details' => [
            'selected_seats' => $selectedSeatsInfo,
            'total_price' => $totalPrice,
        ]
    ];
    }


    
    public function confirmBooking(array $data, User $user)
    {

        $trip = $this->tripRepository->findById($data['trip_id']);
        if (!$trip || $trip->status !== 'scheduled') {
            throw ValidationException::withMessages(['trip_id' => 'Chuyến đi không còn hợp lệ.']);
        }

        $tripSeatsMap = $trip->seats->keyBy('id');
        $totalPrice = 0;
        $selectedSeatsInfo = [];
        
        foreach ($data['seat_ids'] as $seatId) {
            $seat = $tripSeatsMap->get($seatId);
            if (!$seat || $seat->pivot->status !== 'available') {
                throw ValidationException::withMessages(['seat_ids' => "Ghế có ID {$seatId} đã được đặt hoặc không hợp lệ."]);
            }
            $totalPrice += $seat->pivot->price;
            $selectedSeatsInfo[] = [
                'id' => $seat->id,
                'price' => $seat->pivot->price,
                'trip_id' => $data['trip_id'],
            ];
        }

        $bookingData = [
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'pickup_stop_id' => $data['pickup_stop_id'],   // Thêm dòng này
            'dropoff_stop_id' => $data['dropoff_stop_id'], // Thêm dòng này
        ];

        return $this->bookingRepository->create($bookingData, $selectedSeatsInfo);
    }
}