<?php



namespace App\Services;


use App\Models\Location;
use App\Enums\ApiError;
use App\Repositories\LocationRepositoryInterface;


class LocationService {
    protected $locationRepository;

    public function __construct(LocationRepositoryInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function getAllLocation(){
        $location = $this->locationRepository->getAll();
        return $location;
    }

    public function findIdBYName(string $name){
        $location = $this->locationRepository->findByName($name);
        return $location;
    }


}