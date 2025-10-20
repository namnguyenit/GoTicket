<?php

namespace App\Services\Vendor;

use App\Repositories\Vendor\StopRepositoryInterface;

class StopService
{
    protected $stopRepository;
    public function __construct(StopRepositoryInterface $stopRepository){
        $this->stopRepository = $stopRepository;
    }
    public function createStop(array $data){
        $data['vendor_id']= auth()->user()->vendor->id;
        return $this->stopRepository->create($data);
    }
}
