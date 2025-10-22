<?php
namespace App\Repositories;
use App\Models\Trips;


interface TripRepositoryInterface
{
    public function search(array $criteria);
    public function findById(int $id): ?Trips;
    public function findWithStops(int $id): ?Trips;
}
