<?php

namespace App\Services\Elasticsearch;

use App\Models\Trips;
use Elastic\Elasticsearch\Client;
use Illuminate\Pagination\Paginator;

class TripIndexer
{
    public function __construct(private Client $es)
    {
    }

    public function toDocument(Trips $trip): array
    {
        $route = $trip->vendorRoute->route;
        $origin = $route->origin;
        $dest = $route->destination;
        $vehicleType = optional($trip->coaches->first()?->vehicle)->vehicle_type ?? null;
        $seatsAvailable = (int)($trip->seats()->wherePivot('status', 'available')->count());

        $dep = $trip->departure_datetime;
        $vnDep = $dep ? $dep->copy()->timezone('Asia/Ho_Chi_Minh') : null;
        $departureDate = $vnDep ? $vnDep->copy()->startOfDay()->toIso8601String() : null;
        $departureHour = $vnDep ? (int)$vnDep->format('G') : null;

        // coach types only for bus
        $busCoachTypes = [];
        if (($vehicleType ?? '') === 'bus') {
            $busCoachTypes = $trip->coaches
                ->map(fn($c) => (string) $c->coach_type)
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        return [
            'id' => $trip->id,
            'route' => trim($origin->name.' - '.$dest->name),
            'from' => $origin->name,
            'to' => $dest->name,
            'origin_id' => (string) $origin->id,
            'destination_id' => (string) $dest->id,
            'vehicle_type' => $vehicleType,
            'departure_time' => $dep?->toIso8601String(),
            'departure_date' => $departureDate,
            'departure_hour' => $departureHour,
            'arrival_time' => optional($trip->arrival_datetime)?->toIso8601String(),
            'price' => (float) $trip->base_price,
            'vendor_id' => (string) $trip->vendorRoute->vendor_id,
            'seats_available' => $seatsAvailable,
            'bus_coach_types' => $busCoachTypes,
        ];
    }

    public function indexTrip(Trips $trip): void
    {
        $doc = $this->toDocument($trip->load([
            'vendorRoute.route.origin:id,name',
            'vendorRoute.route.destination:id,name',
            'coaches.vehicle:id,vehicle_type'
        ]));
            $this->es->index([
                'index' => 'trips-write',
                'id' => (string)$trip->id,
                'body' => $doc,
                'refresh' => 'false'
            ]);

    }

    public function bulkIndex(Paginator $paginator): void
    {
        $ops = [];
        foreach ($paginator->items() as $trip) {
            $doc = $this->toDocument($trip);
            $ops[] = ['index' => ['_index' => 'trips-write', '_id' => (string)$trip->id]];
            $ops[] = $doc;
        }
        if ($ops) {
            $this->es->bulk(['body' => $ops, 'refresh' => false]);
        }
    }
}
