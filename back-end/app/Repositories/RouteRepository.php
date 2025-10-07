<?php


namespace App\Repositories;
use App\Models\Routes;



class RouteRepository implements RouteRepositoryInterface{
    public function findByLocationIds(int $originId, int $destinationId): ?Routes
    {
        return Routes::where('origin_location_id', $originId)
                     ->where('destination_location_id', $destinationId)
                     ->first();
    }

}