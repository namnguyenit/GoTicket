<?php

namespace App\Services;

use App\Repositories\RouteRepositoryInterface; 
use App\Models\Routes;
use App\Enums\ApiError;
use App\Services\LocationService;

class RouteService{

    protected $routeRepository;
    protected $locationService;
    
    public function __construct(RouteRepositoryInterface $routeRepository , LocationService $locationService)
    {
        $this->routeRepository = $routeRepository;
        $this->locationService = $locationService;
    }

    public function findRouteByLocationIds(string $origin, string $destination): ?Routes
    {
        $originLocation = $this->locationService->findIdBYName($origin);
        $destinationLocation = $this->locationService->findIdBYName($destination);


        if (!$originLocation || !$destinationLocation) {
            return null;
        }
        
        
        return $this->routeRepository->findByLocationIds($originLocation->id, $destinationLocation->id);
    }
}