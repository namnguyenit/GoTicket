<?php


namespace App\Repositories;
use App\Models\Location;



class LocationRepository implements LocationRepositoryInterface{
    public function getAll(){
        return Location::all();
    }


    public function findByName(string $name): ?Location{
        return Location::where('name' , 'LIKE' , "%$name%")->first();
    }
}

