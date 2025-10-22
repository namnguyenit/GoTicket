<?php

namespace App\Repositories\Vendor;

use App\Models\Coaches;

class CoachRepository implements CoachRepositoryInterface
{
    public function create(array $data)
    {
        return Coaches::create($data);
    }

    public function delete(Coaches $coach)
    {
        return $coach->delete();
    }

    public function listByVehicle(int $vehicleId)
    {
        return Coaches::where('vehicle_id', $vehicleId)->orderBy('id')->get();
    }
}
