<?php

namespace App\Repositories\Vendor;

use App\Repositories\Vendor\StopRepositoryInterface;
use App\Models\Stops;

class StopRepository implements StopRepositoryInterface
{

    public function create(array $data)
    {
        return Stops::create($data);
    }
}
