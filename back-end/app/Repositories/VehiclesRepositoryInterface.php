<?php

namespace App\Repositories;


interface VehiclesRepositoryInterface{
    public function findType(int $id);
}