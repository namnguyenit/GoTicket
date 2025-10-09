<?php

namespace App\Repositories\Vendor;
use Illuminate\Support\Collection;
interface ManagerVehicelRepositoryInterface
{
    public function allForVendor(int $vendorId);
}