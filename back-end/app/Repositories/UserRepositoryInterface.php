<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findByEmail(string $email): ?User;
    public function all(?string $role = null);
    public function findByName(string $name);
    public function delete(string $email): bool;
    public function update(User $user , array $data): bool;

    public function createVendor(array $userData, array $vendorData): User;
}