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
use Illuminate\Support\Facades\Log;


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
    $credentials = $request->validated();
    // dd($credentials);
    Log::info('validate :' , $credentials);
    $result = $this->authService->loginUser($credentials);

    // Log::info('erro :' , $result['error']);
    Log::info('erro :' , $result);
    if (isset($result['error'])) {
        // Nếu có key 'error', dùng helper để trả về lỗi cụ thể
        return $this->error($result['error']);
    }

    // Nếu không có lỗi, xử lý thành công
    return $this->success([
        'authorisation' => [
            'token' => $result['token'],
            'type' => 'bearer',
        ]
    ], ApiSuccess::ACTION_SUCCESS);
}
}