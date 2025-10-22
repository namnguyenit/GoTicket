<?php

namespace App\Repositories\Vendor;

interface StopRepositoryInterface
{
    public function create(array $data);
    public function paginateByVendor(int $vendorId, int $perPage = 10, ?string $keyword = null);
    public function update(\App\Models\Stops $stop, array $data);
    public function delete(\App\Models\Stops $stop);
    public function listByVendorWithLocation(int $vendorId, ?string $keyword = null);
    public function listByVendorAndLocation(int $vendorId, int $locationId, ?string $keyword = null);
}
