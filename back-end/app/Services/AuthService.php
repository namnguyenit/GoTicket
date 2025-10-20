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
            return ['error' => ApiError::EMAIL_NOT_EXISTS];
        }

        // Bước 2: KIỂM TRA ĐẶC BIỆT DÀNH CHO NHÀ XE (VENDOR)
        // Nếu user có vai trò là 'vendor', kiểm tra trạng thái của nhà xe đó
        if ($user->role === 'vendor') {
            // Dùng Eager Loading để lấy thông tin vendor liên quan
            $user->load('vendor'); 
            
            // Nếu không có thông tin vendor hoặc trạng thái không phải là 'active'
            if (! $user->vendor || $user->vendor->status !== 'active') {
                // Trả về lỗi, không cho đăng nhập
                return ['error' => ApiError::ACCOUNT_INACTIVE];
            }
        }
        
        // Bước 3: Nếu mọi thứ ổn, thử xác thực mật khẩu và tạo token
        if (! $token = auth('api')->attempt($credentials)) {
            // Nếu `attempt` thất bại, có nghĩa là mật khẩu sai
            return ['error' => ApiError::WRONG_PASSWORD];
        }

        // Bước 4: Nếu thành công, trả về token
        return ['token' => $token];
    }

    public function getMyAccount(): ?User
    {
        // Hàm auth('api')->user() sẽ tự động:
        // 1. Đọc token từ header.
        // 2. Xác thực token.
        // 3. Lấy user ID từ token.
        // 4. Truy vấn database và trả về đối tượng User tương ứng.
        return auth('api')->user();
    }
}