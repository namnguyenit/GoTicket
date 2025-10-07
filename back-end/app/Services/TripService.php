<?php
namespace App\Services;
use App\Repositories\TripRepositoryInterface;

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
}