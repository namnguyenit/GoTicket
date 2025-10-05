<?php

namespace App\Repositories;

use App\Models\Vehicles;


class VehiclesRepository implements VehiclesRepositoryInterface{
    public function findType(int $id){
        $type = VendorRoute::where('id' , 'LIKE' , "$id")->first();
        return $type->vehicle_type;
    }
}