<?php

namespace App\Repositories;

use App\Models\Trips;
use App\Services\Elasticsearch\TripSearchService;

class TripSearchRepository implements TripRepositoryInterface
{
    public function __construct(private TripSearchService $service)
    {
    }

    public function search(array $criteria)
    {
        $result = $this->service->search($criteria);
        return app(\App\Services\Elasticsearch\TripResultHydrator::class)->hydrate($result);
    }

    public function findById(int $id): ?Trips
    {
        return app(TripRepository::class)->findById($id);
    }

    public function findWithStops(int $id): ?Trips
    {
        return app(TripRepository::class)->findWithStops($id);
    }
}
