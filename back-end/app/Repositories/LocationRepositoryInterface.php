<?php

namespace App\Repositories;

use App\Models\Location;

interface LocationRepositoryInterface{
   public function findByName(string $name): ?Location;
   public function getAll();
}