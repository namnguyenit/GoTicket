<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;

class AuthService
{
    protected $userRepository;

    // "Tiêm" UserRepository vào đây
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function registerUser(array $data): ?string
    {
        $user = $this->userRepository->create($data);

        // Tự động đăng nhập và tạo token
        $token = auth('api')->login($user);

        return $token;
    }
}