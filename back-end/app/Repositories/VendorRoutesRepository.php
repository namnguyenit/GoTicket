<?php

namespace App\Repositories;

use App\Models\VendorRoute;


class VendorRoutesRepository implements VendorRoutesRepositoryInterface{
    public function findvendorRoutes(int $id){
        return VendorRoute::where('id' , 'LIKE' , "$id")->get();
    }
}