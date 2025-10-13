<?php

namespace App\Repositories;

use App\Models\Bookings;
use App\Models\User;

interface BookingRepositoryInterface
{
    /**
     * Tạo một booking mới và các chi tiết liên quan trong một transaction.
     *
     * @param array $bookingData Dữ liệu cho bảng bookings
     * @param array $seatDetails Mảng chi tiết các ghế được đặt
     * @return Bookings
     */
    public function create(array $bookingData, array $seatDetails): Bookings;
}