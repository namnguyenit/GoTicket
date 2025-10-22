<?php

namespace App\Services\Vendor;

use App\Models\Trips;
use App\Models\VendorRoute;
use App\Models\Stops;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TripService
{
    public function listTripsByVendor(int $perPage = 10)
    {
        $vendorId = Auth::user()->vendor->id;
        return Trips::query()
            ->whereHas('vendorRoute', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })
            ->orderByDesc('departure_datetime')
            ->paginate($perPage);
    }

    public function createTrip(array $data): Trips
    {
        return DB::transaction(function () use ($data) {
            $vendorId = Auth::user()->vendor->id;

            $vendorRoute = VendorRoute::where('id', $data['vendor_route_id'] ?? 0)
                ->where('vendor_id', $vendorId)
                ->firstOrFail();

            $trip = Trips::create([
                'vendor_route_id' => $vendorRoute->id,
                'departure_datetime' => Carbon::parse($data['departure_datetime']),
                'arrival_datetime' => Carbon::parse($data['arrival_datetime']),
                'base_price' => $data['base_price'],
                'status' => $data['status'] ?? 'scheduled',
            ]);

            // Stops (optional)
            if (!empty($data['stops']) && is_array($data['stops'])) {
                $attach = [];
                foreach ($data['stops'] as $item) {
                    // đảm bảo stop thuộc vendor hiện tại
                    $stop = Stops::where('id', $item['stop_id'])
                        ->where('vendor_id', $vendorId)
                        ->firstOrFail();
                    $attach[$stop->id] = [
                        'stop_type' => $item['stop_type'],
                        'scheduled_time' => Carbon::parse($item['scheduled_time']),
                    ];
                }
                $trip->stops()->attach($attach);
            }

            return $trip->load(['stops']);
        });
    }

    public function getVendorTrip(Trips $trip): ?Trips
    {
        $vendorId = Auth::user()->vendor->id;
        $owns = $trip->vendorRoute()->where('vendor_id', $vendorId)->exists();
        return $owns ? $trip : null;
    }

    public function updateTrip(Trips $trip, array $data): Trips
    {
        return DB::transaction(function () use ($trip, $data) {
            $vendorId = Auth::user()->vendor->id;
            // verify ownership
            if (!$trip->vendorRoute()->where('vendor_id', $vendorId)->exists()) {
                abort(403);
            }

            // If vendor_route_id provided, ensure ownership and update
            if (!empty($data['vendor_route_id'])) {
                $vendorRoute = VendorRoute::where('id', $data['vendor_route_id'])
                    ->where('vendor_id', $vendorId)
                    ->firstOrFail();
                $trip->vendor_route_id = $vendorRoute->id;
            }

            if (isset($data['departure_datetime'])) {
                $trip->departure_datetime = Carbon::parse($data['departure_datetime']);
            }
            if (isset($data['arrival_datetime'])) {
                $trip->arrival_datetime = Carbon::parse($data['arrival_datetime']);
            }
            if (isset($data['base_price'])) {
                $trip->base_price = $data['base_price'];
            }
            if (isset($data['status'])) {
                $trip->status = $data['status'];
            }
            $trip->save();

            // Replace stops if provided
            if (array_key_exists('stops', $data)) {
                $sync = [];
                foreach ((array) $data['stops'] as $item) {
                    $stop = Stops::where('id', $item['stop_id'])
                        ->where('vendor_id', $vendorId)
                        ->firstOrFail();
                    $sync[$stop->id] = [
                        'stop_type' => $item['stop_type'],
                        'scheduled_time' => Carbon::parse($item['scheduled_time']),
                    ];
                }
                $trip->stops()->sync($sync);
            }

            return $trip->load(['stops']);
        });
    }

    public function cancelTrip(Trips $trip): void
    {
        $vendorId = Auth::user()->vendor->id;
        if (!$trip->vendorRoute()->where('vendor_id', $vendorId)->exists()) {
            abort(403);
        }
        $trip->status = 'cancelled';
        $trip->save();
    }
}
