<?php

namespace App\Repositories\Vendor;

use App\Models\Coaches;

class CoachRepository implements CoachRepositoryInterface
{
    public function create(array $data)
    {
        return Coaches::create($data);
    }
}
