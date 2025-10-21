<?php

namespace App\Repositories\Vendor;
use App\Models\Vehicles;
use Illuminate\Support\Collection;
interface ManagerVehicleRepositoryInterface
{
    public function showAllVehicles(int $vendorId);
    public function create(array $data);
    public function getByVendor(int $vendorId);
    public function update(Vehicles $vehicle, array $data);
    public function delete(Vehicles $vehicle);
}
