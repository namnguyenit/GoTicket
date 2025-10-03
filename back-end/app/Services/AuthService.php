<?php

namespace App\Services;

use App\Repositories\UserRepositoryInterface;
use App\Models\User;
use App\Enums\ApiError;


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

    public function loginUser(array $credentials): array
    {
        // Bước 1: Kiểm tra xem user có tồn tại không
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user) {
            // Nếu không tìm thấy, trả về lỗi USER_NOT_FOUND
            return ['error' => ApiError::EMAIL_NOT_EXISTS];
        }

        // Bước 2: Nếu user tồn tại, thử xác thực mật khẩu
        if (! $token = auth('api')->attempt($credentials)) {
            // Nếu `attempt` thất bại, có nghĩa là mật khẩu sai
            return ['error' => ApiError::WRONG_PASSWORD];
        }

        // Bước 3: Nếu thành công, trả về token
        return ['token' => $token];
    }
}