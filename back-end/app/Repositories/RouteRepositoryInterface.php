<?php

namespace App\Repositories;

use App\Models\Routes;

interface RouteRepositoryInterface
{
    public function findByLocationIds(int $originId, int $destinationId): ?Routes;
}