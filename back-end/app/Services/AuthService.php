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

    public function loginUser(array $credentials): ?string
    {
        // Dùng guard 'api' để thử xác thực
        // Hàm `attempt` sẽ tự động hash password và so sánh
        if (! $token = auth('api')->attempt($credentials)) {
            // Nếu không khớp, trả về null
            return null;
        }

        // Nếu khớp, trả về token
        return $token;
    }
}