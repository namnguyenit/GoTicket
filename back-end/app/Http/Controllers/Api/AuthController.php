<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiError;
use App\Enums\ApiSuccess;
use App\Http\Requests\Api\LoginRequest;


class AuthController extends Controller
{
    use ResponseHelper;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterUserRequest $request)
    {
        // Dữ liệu đã được validate tự động bởi RegisterUserRequest
        $validatedData = $request->validated();

        $user = $this->authService->registerUser($validatedData);

        

        return $this->success([
            'user' => new UserResource($user)
            
        ], ApiSuccess::CREATED_SUCCESS);
    }


    public function login(LoginRequest $request)
    {
        // Dữ liệu đã được validate (email, password phải tồn tại)
        $credentials = $request->validated();

        // Giao việc cho Service xử lý
        $token = $this->authService->loginUser($credentials);

        // Kiểm tra kết quả Service trả về
        if (! $token) {
            // Nếu không có token, nghĩa là sai email/password
            // Trả về lỗi xác thực
            return $this->error(ApiError::AUTHENTICATION_FAILED);
        }

        // Nếu có token, trả về response thành công
        return $this->success([
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], ApiSuccess::ACTION_SUCCESS);
    }
}