<?php

namespace App\Repositories\Vendor;

use App\Repositories\Vendor\SeatRepositoryInterface;
use App\Models\Seats;
class SeatRepository implements SeatRepositoryInterface
{

    public function createMany(array $seatsData)
    {
        return Seats::insert($seatsData);
    }
}
