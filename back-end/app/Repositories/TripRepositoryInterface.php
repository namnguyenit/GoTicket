<?php
namespace App\Repositories;

interface TripRepositoryInterface
{
    // Chúng ta sẽ truyền 1 mảng các điều kiện tìm kiếm vào đây
    public function search(array $criteria);
}