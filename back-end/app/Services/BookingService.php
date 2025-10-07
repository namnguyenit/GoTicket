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

    /**
     * ✅ THÊM PHƯƠNG THỨC NÀY VÀO
     *
     * Khởi tạo và xác thực thông tin đặt vé trước khi tới trang thanh toán.
     *
     * @param int $tripId
     * @param array $seatIds
     * @param User $user
     * @return array
     * @throws ValidationException
     */
    public function initiateBooking(int $tripId, array $seatIds, User $user): array
    {
        // 1. Lấy thông tin chuyến đi kèm tất cả các ghế và trạng thái của chúng
        $trip = $this->tripRepository->findById($tripId);

        if (!$trip || $trip->status !== 'scheduled') {
            throw ValidationException::withMessages([
                'trip_id' => 'Chuyến đi không hợp lệ hoặc không còn tồn tại.'
            ]);
        }

        // 2. Tạo một map để tra cứu thông tin ghế của chuyến đi nhanh hơn
        $tripSeatsMap = $trip->seats->keyBy('id');
        
        $totalPrice = 0;
        $selectedSeatsInfo = [];
        $unavailableSeats = [];

        foreach ($seatIds as $seatId) {
            $seat = $tripSeatsMap->get($seatId);

            // 3. Kiểm tra xem ghế có thuộc chuyến đi không và có trống không
            if (!$seat || $seat->pivot->status !== 'available') {
                $unavailableSeats[] = $seatId;
                continue;
            }

            // 4. Nếu hợp lệ, tính tổng tiền và thu thập thông tin
            $totalPrice += $seat->pivot->price;
            $selectedSeatsInfo[] = [
                'id' => $seat->id,
                'seat_number' => $seat->seat_number,
                'price' => $seat->pivot->price,
            ];
        }

        // 5. Nếu có bất kỳ ghế nào không hợp lệ, ném lỗi
        if (!empty($unavailableSeats)) {
            throw ValidationException::withMessages([
                'seat_ids' => 'Một hoặc nhiều ghế bạn chọn không hợp lệ hoặc đã được đặt.',
                'unavailable_seats' => $unavailableSeats
            ]);
        }

        // 6. Nếu mọi thứ ổn, trả về dữ liệu cho trang thanh toán
        return [
        'user_info' => [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
        ],
        'trip_info' => [
            'id' => $trip->id,
            // SỬA DÒNG NÀY:
            // 'vendor_name' => $trip->vendorRoute->vendor->user->name,

            // BẰNG DÒNG NÀY:
            // Dùng toán tử `?->` (optional chaining) để truy cập an toàn.
            // Nếu bất kỳ phần nào là null, kết quả sẽ là null.
            // Sau đó dùng `??` (null coalescing) để gán giá trị mặc định.
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

    // ... (phương thức initiateBooking giữ nguyên)

    /**
     * Xác nhận và tạo booking.
     *
     * @param int $tripId
     * @param array $seatIds
     * @param User $user
     * @return \App\Models\Bookings
     * @throws ValidationException
     */
    public function confirmBooking(array $data, User $user)
    {
        // BƯỚC KIỂM TRA LẠI CUỐI CÙNG
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

        // ✅ TỔ CHỨC LẠI DỮ LIỆU ĐỂ TRUYỀN XUỐNG REPOSITORY
        $bookingData = [
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'pickup_stop_id' => $data['pickup_stop_id'],   // Thêm dòng này
            'dropoff_stop_id' => $data['dropoff_stop_id'], // Thêm dòng này
        ];

        return $this->bookingRepository->create($bookingData, $selectedSeatsInfo);
    }
}