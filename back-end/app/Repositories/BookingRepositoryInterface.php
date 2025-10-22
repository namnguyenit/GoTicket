<?php

namespace App\Repositories;

use App\Models\Bookings;
use App\Models\User;

interface BookingRepositoryInterface
{
    
    public function create(array $bookingData, array $seatDetails): Bookings;
}