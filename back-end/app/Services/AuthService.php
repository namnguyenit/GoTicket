<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use App\Models\User;

class AuthService
{
    protected $userRepository;

    // "Tiêm" UserRepository vào đây
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(array $data): User
    {
        $user = $this->userRepository->create($data);

        return $user;
    }
}