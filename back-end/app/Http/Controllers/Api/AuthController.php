<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Http\Helpers\ResponseHelper;
use App\Enums\ApiError;

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

        $token = $this->authService->registerUser($validatedData);

        // Lấy lại thông tin user vừa tạo
        $user = auth('api')->user();

        return $this->success([
            'user' => new UserResource($user),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 'Đăng ký tài khoản thành công', 201);
    }
}