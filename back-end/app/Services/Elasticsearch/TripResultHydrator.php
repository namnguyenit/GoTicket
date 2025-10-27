<?php

namespace App\Services\Elasticsearch;

use App\Models\Trips;
use Illuminate\Pagination\LengthAwarePaginator;

class TripResultHydrator
{
    public function hydrate(array $esResult): LengthAwarePaginator
    {
        $ids = array_map(fn($it) => (int)($it['id'] ?? 0), $esResult['items'] ?? []);
        $ids = array_values(array_filter($ids));
        $perPage = (int)($esResult['per_page'] ?? 12);
        $page = (int)($esResult['current_page'] ?? 1);
        $total = (int)($esResult['total'] ?? 0);
        if (!$ids) {
            return new LengthAwarePaginator(collect(), $total, $perPage, $page, [
                'path' => request()->url(), 'query' => request()->query(),
            ]);
        }
        $trips = Trips::query()
            ->with([
                'vendorRoute.vendor.user:id,name',
                'vendorRoute.route.origin',
                'vendorRoute.route.destination',
                'coaches.vehicle',
            ])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $ordered = collect($ids)->map(fn($id) => $trips->get($id))->filter();
        return new LengthAwarePaginator($ordered, $total, $perPage, $page, [
            'path' => request()->url(), 'query' => request()->query(),
        ]);
    }
}
