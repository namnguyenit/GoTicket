<?php
namespace App\Services;
use App\Repositories\TripRepositoryInterface;
use App\Models\Trips;

class TripService
{
    protected $tripRepository;
    public function __construct(TripRepositoryInterface $tripRepository)
    {
        $this->tripRepository = $tripRepository;
    }

    public function searchTrips(array $criteria)
    {
        return $this->tripRepository->search($criteria);
    }


    public function getTripById(int $id): ?Trips
    {
        return $this->tripRepository->findById($id);
    }

    public function getTripStops(int $id): ?Trips
    {
        return $this->tripRepository->findWithStops($id);
    }
}