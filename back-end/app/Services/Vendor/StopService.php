<?php

namespace App\Services\Vendor;

use App\Repositories\Vendor\StopRepositoryInterface;
use App\Models\Stops;
use Illuminate\Support\Facades\Auth;

class StopService
{
    protected $stopRepository;
    public function __construct(StopRepositoryInterface $stopRepository){
        $this->stopRepository = $stopRepository;
    }
    public function createStop(array $data){
        $data['vendor_id']= Auth::user()->vendor->id;
        return $this->stopRepository->create($data);
    }

    public function listStopsByVendor(int $perPage = 10, ?string $keyword = null, ?string $transportType = null)
    {
        $vendorId = Auth::user()->vendor->id;
        return $this->stopRepository->paginateByVendor($vendorId, $perPage, $keyword, $transportType);
    }

    public function updateStop(Stops $stop, array $data): Stops
    {
        return $this->stopRepository->update($stop, $data);
    }

    public function deleteStop(Stops $stop): bool
    {
        return (bool) $this->stopRepository->delete($stop);
    }

    public function listAllStopsGroupedByLocation(?string $keyword = null, ?string $transportType = null): array
    {
        $vendorId = Auth::user()->vendor->id;
        $rows = $this->stopRepository->listByVendorWithLocation($vendorId, $keyword, $transportType);

        $grouped = [];
        foreach ($rows as $row) {
            $locId = $row->location_id;
            if (!isset($grouped[$locId])) {
                $grouped[$locId] = [
                    'location_id' => $locId,
                    'location_name' => $row->location_name,
                    'stops' => [],
                ];
            }
            $grouped[$locId]['stops'][] = [
                'id' => $row->id,
                'name' => $row->name,
                'address' => $row->address,
                'location_id' => $row->location_id,
                'vendor_id' => $row->vendor_id,
            ];
        }

        return array_values($grouped);
    }

    public function listStopsByVendorAndLocation(int $locationId, ?string $keyword = null)
    {
        $vendorId = Auth::user()->vendor->id;
        return $this->stopRepository->listByVendorAndLocation($vendorId, $locationId, $keyword);
    }
}
