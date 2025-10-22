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
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Api\UpdateProfileRequest;

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
    public function getInfoAccout()
    {
        // Giao việc cho Service
        $user = $this->authService->getMyAccount();

        // Nếu Service không tìm thấy user (token không hợp lệ)
        if (!$user) {
            return $this->error(ApiError::UNAUTHORIZED);
        }

        // Nếu thành công, trả về thông tin user qua UserResource
        return $this->success(
            new UserResource($user),
            ApiSuccess::GET_DATA_SUCCESS
        );
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth('api')->user();
        $validatedData = $request->validated();

        try {
            // Gọi service để thực hiện việc cập nhật
            $this->authService->updateProfile($user, $validatedData);

            // Trả về thông tin người dùng đã được cập nhật
            return $this->success(new UserResource($user), ApiSuccess::ACTION_SUCCESS);

        } catch (ValidationException $e) {
            // Bắt lỗi Validation từ Service (ví dụ: sai mật khẩu cũ)
            // và trả về lỗi 422 với thông báo cụ thể
            return $this->error(ApiError::VALIDATION_FAILED, $e->errors());
        }
    }
}