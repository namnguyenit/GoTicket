<?php

namespace App\Repositories\Vendor;

use App\Repositories\Vendor\StopRepositoryInterface;
use App\Models\Stops;
use Illuminate\Support\Facades\DB;

class StopRepository implements StopRepositoryInterface
{

    public function create(array $data)
    {
        return Stops::create($data);
    }

    public function paginateByVendor(int $vendorId, int $perPage = 10, ?string $keyword = null)
    {
        $query = Stops::query()->where('vendor_id', $vendorId);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('address', 'like', "%{$keyword}%");
            });
        }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function update(Stops $stop, array $data)
    {
        $stop->update($data);
        return $stop;
    }

    public function delete(Stops $stop)
    {
        return $stop->delete();
    }

    public function listByVendorWithLocation(int $vendorId, ?string $keyword = null)
    {
        $query = Stops::query()
            ->leftJoin('locations', 'locations.id', '=', 'stops.location_id')
            ->where('stops.vendor_id', $vendorId)
            ->select('stops.*', 'locations.name as location_name')
            ->orderBy('locations.name')
            ->orderBy('stops.name');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('stops.name', 'like', "%{$keyword}%")
                  ->orWhere('stops.address', 'like', "%{$keyword}%");
            });
        }

        return $query->get();
    }

    public function listByVendorAndLocation(int $vendorId, int $locationId, ?string $keyword = null)
    {
        $query = Stops::query()
            ->where('vendor_id', $vendorId)
            ->where('location_id', $locationId)
            ->orderBy('name');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('address', 'like', "%{$keyword}%");
            });
        }

        return $query->get();
    }
}
