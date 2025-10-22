<?php

namespace App\Repositories\Vendor;

interface CoachRepositoryInterface
{
    public function create(array $data);
    public function delete(\App\Models\Coaches $coach);
    public function listByVehicle(int $vehicleId);
}
