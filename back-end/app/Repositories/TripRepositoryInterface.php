<?php
namespace App\Repositories;
use App\Models\Trips;


interface TripRepositoryInterface
{
    // Chúng ta sẽ truyền 1 mảng các điều kiện tìm kiếm vào đây
    public function search(array $criteria);
    public function findById(int $id): ?Trips;
    public function findWithStops(int $id): ?Trips;
}