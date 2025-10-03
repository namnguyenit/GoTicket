<?php

namespace App\Repositories;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findByEmail(string $email): ?User;
    public function all();
    public function findByName(string $name);
    public function delete(string $email): bool;
    public function update(string $email , array $data): bool;

}